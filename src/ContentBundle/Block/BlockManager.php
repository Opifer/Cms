<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Gedmo\Timestampable\TimestampableListener;
use Opifer\CmsBundle\EventListener\LoggableListener;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Toolset;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Repository\BlockLogEntryRepository;
use Opifer\Revisions\EventListener\RevisionListener;
use Opifer\Revisions\Exception\DeletedException;
use Opifer\Revisions\RevisionManager;

/**
 * Class BlockManager
 *
 * This class provides methods mainly for managing blocks inside of the editor at a specific
 * version. It takes care of applying the changeset from BlockLogEntry to create a real-time
 * state of the Block instance before publishing/persisting it.
 *
 * @package Opifer\ContentBundle\Manager
 */
class BlockManager
{
    /** @var array */
    protected $services;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var RevisionManager */
    protected $revisionManager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, RevisionManager $revisionManager)
    {
        $this->em = $em;
        $this->revisionManager = $revisionManager;
    }

    /**
     * Get repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('OpiferContentBundle:Block');
    }

    /**
     * Adds all the block services, tagged with 'opifer.content.block_manager'
     *
     * @param BlockServiceInterface $service
     * @param string                $alias
     */
    public function addService(BlockServiceInterface $service, $alias)
    {
        $this->services[$alias] = $service;
    }

    /**
     * Get all registered services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return Toolset
     */
    public function getToolset()
    {
        $toolbelt = new Toolset;

        foreach ($this->services as $service) {
            if ($service instanceof ToolsetMemberInterface) {
                $toolbelt->addTool($service->getTool());
            }
        }

        return $toolbelt;
    }

    /**
     * Returns the block service by the block's id
     *
     * @param integer $id
     *
     * @return BlockServiceInterface
     */
    public function getServiceByBlockId($id)
    {
        $block = $this->find($id);

        return $this->getService($block);
    }

    /**
     * Returns the block service
     *
     * @param string|BlockInterface  $block
     *
     * @return BlockServiceInterface
     *
     * @throws \Exception
     */
    public function getService($block)
    {
        $blockType = ($block instanceof BlockInterface) ? $block->getBlockType() : $block;
        if (!isset($this->services[$blockType])) {
            throw new \Exception(sprintf("No BlockService available by the alias %s, available: %s", $blockType, implode(', ', array_keys($this->services))));
        }

        return $this->services[$blockType];
    }

    /**
     * Find a Block in the repository with optional specified version.
     *
     * @param integer      $id
     * @param bool         $draft
     *
     * @return BlockInterface
     */
    public function find($id, $draft = false)
    {
        if ($draft) {
            $this->setDraftVersionFilter(! $draft);
        }

        $block = $this->getRepository()->find($id);

        if ($draft) {
            if (null !== $revision = $this->revisionManager->getDraftRevision($block)) {
                $this->revisionManager->revert($block, $revision);
            }
        }

        return $block;
    }

    /**
     * Find a Block in the repository in optional draft
     *
     * @param integer  $id
     * @param bool     $draft
     *
     * @return BlockInterface
     */
    public function findById($id, $draft = true)
    {
        $blocks = $this->getRepository()->findById($id);

        if ($draft) {
            foreach ($blocks as $block) {
                if (null !== $revision = $this->revisionManager->getDraftRevision($block)) {
                    $this->revisionManager->revert($block, $revision);
                }
            }
        }

        return $blocks;
    }


    public function findByOwner(BlockOwnerInterface $owner, $draft = true)
    {
        $this->setDraftVersionFilter(! $draft);

        $blocks = $owner->getBlocks();

        if ($draft) {
            $this->revertToDraft($blocks);
        }

        return $blocks;
    }

    public function revertToDraft($blocks)
    {
        if (is_array($blocks)) {
            $blocks = array($blocks);
        }

        foreach ($blocks as $key => $block) {
            if (null !== $revision = $this->revisionManager->getDraftRevision($block)) {
                try {
                    $this->revisionManager->revert($block, $revision);
                } catch (DeletedException $e) {
                    unset($blocks[$key]);
                }
            }
        }

        return $blocks;
    }

    protected function findGroupDraftRevision($block)
    {
        $revision = null;
        $family = $block->getOwner()->getBlocks();
        foreach ($family as $member) {
            if (null !== $revision = $this->revisionManager->getDraftRevision($member)) {
                break;
            }
        }

        return $revision;
    }

    /**
     * Publishes a block
     *
     * TODO: cleanup created and deleted blocks in revision that were never published.
     *
     * @param BlockInterface|array $blocks
     */
    public function publish($blocks)
    {
        if (is_array($blocks)) {
            $blocks = array($blocks);
        }

        $revision = null;
        $family = $blocks[0]->getOwner()->getBlocks();
        foreach ($family as $member) {
            if (null !== $revision = $this->revisionManager->getDraftRevision($member)) {
                break;
            }
        }

        if ($revision) {
            $this->killRevisionListener();

            foreach ($blocks as $block) {
                try {
                    $this->revisionManager->revert($block, $revision);
                } catch (DeletedException $e) {
                    $this->em->remove($block);
                }

                $this->em->flush($block);
            }

            $this->revisionManager->setRevisionDraft($revision, false);
        }
    }

    /**
     * @param BlockInterface $block
     *
     * @return BlockManager
     */
    public function save(BlockInterface $block, $draft = true)
    {
        if ($draft) {
            $this->setDraftVersionFilter(! $draft);
            $this->killTimestampableListener();
            $block->setDraft($draft);

            $revision = null;
            $family = $block->getOwner()->getBlocks();
            foreach ($family as $member) {
                if (null !== $revision = $this->revisionManager->getDraftRevision($member)) {
                    break;
                }
            }

            $block->revision = $revision;
        }

        if (! $this->em->contains($block)) {
            $this->em->persist($block);
        }
        $this->em->flush($block);

        return $this;
    }


    /**
     * @param BlockInterface $block
     */
    public function remove(BlockInterface $block, $draft = true)
    {
        $block->setDraft($draft);

        if ($draft) {
            $block->revision = $this->findGroupDraftRevision($block);
        }

        $this->em->remove($block);
        $this->em->flush($block);
    }

    /**
     * Clones an entire tree and persists to database
     *
     * @param BlockOwnerInterface $block
     *
     * @return BlockOwnerInterface
     */
    public function duplicate(BlockOwnerInterface $block)
    {
        $this->setDraftVersionFilter(false);

        $blocks = $this->findByOwner($block);
        array_unshift($blocks, $block);

        // 1. Interate over all owned blocks and disconnect parents keeping ids
        /** @var Block $descendant */
        foreach ($blocks as $descendant) {
            $descendant->originalId = $descendant->getId();

            // if it has a parent we need to put it somewhere
            if ($descendant->getParent()) {
                $descendant->originalParentId = $descendant->getParent()->getId();
                $descendant->setParent(null);
            }

            if ($descendant instanceof BlockContainerInterface) {
                $descendant->setChildren(null);
            }

            $descendant->setOwner($block);

            $this->em->detach($descendant);
            $this->em->persist($descendant);
        }
        $this->em->flush();

        // 2. Iterate over all new blocks and reset their parents
        foreach ($blocks as $descendant) {
            if (isset($descendant->originalParentId)) {
                foreach ($blocks as $parent) {
                    if ($descendant->originalParentId === $parent->originalId) {
                        $descendant->setParent($parent);
                        $parent->addChild($descendant);
                    }
                }
            }
        }
        $this->em->flush();

        return $block;
    }

    /**
     * @param integer $ownerId
     */
    public function discardAll($ownerId)
    {
        $owner = $this->find($ownerId);
        if (!$owner instanceof BlockOwnerInterface) {
            throw new \Exception('Discard all changes is only possible on Block owners');
        }

        $this->em->getRepository('OpiferContentBundle:BlockLogEntry')->discardAll($ownerId, 0);

        $stubs = $this->getRepository()->findBy(['owner' => $owner, 'version' => 0]);

        foreach ($stubs as $stub) {
            $this->em->remove($stub);
        }

        $this->em->flush();
    }

    /**
     * Kills the DraftVersionFilter
     */
    public function setDraftVersionFilter($enabled = true)
    {
        if ($this->em->getFilters()->isEnabled('draft') && ! $enabled) {
            $this->em->getFilters()->disable('draft');
        } else if (! $this->em->getFilters()->isEnabled('draft') && $enabled) {
            $this->em->getFilters()->enable('draft');
        }
    }


    /**
     * Removes the TimestampableListener from the EventManager
     */
    public function killTimestampableListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if ($listener instanceof TimestampableListener) {
                    $listenerInst = $listener;
                    break 2;
                }
            }
        }
        $this->em->getEventManager()->removeEventListener(array('onFlush'), $listenerInst);
    }

    /**
     * Removes the RevisionListener from the EventManager
     */
    public function killRevisionListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if ($listener instanceof RevisionListener) {
                    $listenerInst = $listener;
                    break 2;
                }
            }
        }
        $this->em->getEventManager()->removeEventListener(array(Events::onFlush, Events::postPersist, Events::postUpdate, Events::postFlush, SoftDeleteableListener::POST_SOFT_DELETE), $listenerInst);
    }


    /**
     * TODO: refactor this
     *
     * @param BlockOwnerInterface $owner
     * @param string              $type
     * @param integer             $parentId
     * @param integer             $placeholder
     * @param array               $sort
     * @param null|array          $data
     *
     * @throws \Exception
     *
     * @return BlockInterface
     */
    public function createBlock($owner, $type, $parentId, $placeholder, $sort, $data = null, $draft = true)
    {
        $className = $this->em->getClassMetadata($type)->getName();
        $block = new $className();
        $parent = $parentId ? $this->find($parentId, $draft) : null;

        $block->setPosition($placeholder);
        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setSortParent(-1); // < need to be calculated when putting in the tree

        // Set owner
        $block->setOwner($owner);
        $owner->addBlock($block);

        // This should replaced with a more hardened function
        if ($data) {
            foreach ($data as $attribute => $value) {
                $reflProp = new \ReflectionProperty($block, $attribute);
                $reflProp->setAccessible(true);
                $reflProp->setValue($block, $value);
            }
        }

        $block->setParent($parent);

        // Save now, rest will be in changeset. All we do is a create a stub entry anyway.
        $this->save($block, $draft);

        if (count($sort) > 1) {
            // Replace the zero value in the posted sort array
            // with the newly created id to perform sorting
            $id = $block->getId();
            $sort = array_map(
                function ($v) use ($id) {
                    return $v == "" || $v == "0" ? $id : $v;
                },
                $sort
            );


            array_walk($sort, function (&$id) { $id = (int) $id; });
            $contained = $this->findById($sort, $draft);

            if ($contained) {
                $contained = $this->setSortsByDirective($contained, $sort);

                foreach ($contained as $node) {
                    if ($node->getOwner()->getId() == $block->getOwner()->getId()) {
                        $this->save($node, $draft);
                    }
                }
            }
        } else {
        }

        return $block;
    }

    /**
     * Move a block to a new parent in a specific placeholder and order (sort)
     *
     * This method performs the change and persists/flushes to the database.
     *
     * @param integer $id
     * @param integer $parentId
     * @param integer $placeholder
     * @param array   $sort
     */
    public function moveBlock($id, $parentId, $placeholder, $sort, $draft = true)
    {
        $block = $this->find($id, $draft);

        $parent = ($parentId) ? $this->find($parentId) : null;

        $block->setPosition($placeholder);

        $block->setParent($parent);

        if ($parent) {
            $parent->addChild($block);
        }

        $this->save($block);

        array_walk($sort, function (&$id) { $id = (int) $id; });
        $contained = $this->findById($sort, $draft);

        if ($contained) {
            $contained = $this->setSortsByDirective($contained, $sort);

            foreach ($contained as $node) {
                if ($node->getOwner()->getId() == $block->getOwner()->getId()) {
                    $this->save($node);
                }
            }
        }
    }

    /**
     * Makes a block shared and creates a pointer block in it's place
     *
     * This method performs the change and persists/flushes to the database.
     *
     * @param integer $id
     * @param integer $rootVersion
     *
     * @return BlockPointer
     */
    public function makeBlockShared($id)
    {
        $block = $this->find($id, false);

        // Duplicate some of the settings to the pointer
        $pointer = new PointerBlock();
        $pointer->setOwner($block->getOwner());
        $pointer->setParent($block->getParent());
        $pointer->setReference($block);

        // Detach and make shared
        $block->setShared(true);
        $block->setParent(null);
        $block->setOwner(null);
        $block->setPosition(0);
        $block->setSort(0);

        $this->save($block)->save($pointer);

        return $pointer;
    }

    /**
     * Gets the Block nodes siblings at a version.
     *
     * Retrieving siblings from the database could be simple if we did not need to take
     * the changesets into account, because then we would just get all blocks with the same
     * parent. However the changesets in BlockLogEntry probably store changed parents and
     * so they must be applied on the entire tree first before we can tell.
     *
     * @param BlockInterface $block
     * @param integer        $version
     *
     * @return false|ArrayCollection
     */
    public function getSiblings(BlockInterface $block, $version = false)
    {
        $owner = $block->getOwner();
        $family = $this->findByOwner($owner, $version);

        $siblings = array();

        foreach ($family as $member) {
            if ($member->getParent() && $member->getParent()->getId() == $block->getParent()->getId()) {
                array_push($siblings, $member);
            }
        }

        return $siblings;
    }


    public function revertRecursiveFromNode(BlockInterface $block, $version)
    {
        if (!$block->getOwner()) {
            $this->revert($block, $version);

            return $block;
        }

        $owner = $block->getOwner();
        $this->revert($owner, $version);

        $iterator = new \RecursiveIteratorIterator(
            new RecursiveBlockIterator(
                $owner->getChildren()
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($block->getId() == $item->getId()) {
                return $item;
            }
        }

        throw new \Exception(sprintf('Unable to revert with recursion. Could not find your node %s in the tree anymore', $block));
    }

    /**
     * @param array $blocks
     * @param array $sort
     *
     * @return array
     */
    public function setSortsByDirective($blocks, $sort)
    {
        $sort = array_values($sort); // we're using keys as positions
        $levels = array();

        // Split arrays into levels by owner (child - parent)
        foreach ($blocks as $block) {
            $pos = array_search($block->getId(), $sort, false);
            $levels[$block->getOwner()->getId()][$pos] = $block->getId();
        }

        foreach ($levels as &$level) {
            ksort($level);
        }

        foreach ($blocks as $block) {
            $sortedIds = $levels[$block->getOwner()->getId()];
            $pos = array_search(array_search($block->getId(), $sortedIds), array_keys($sortedIds));
            $block->setSort($pos);

            if ($block->getOwner()->getSuper()) {
                $superId = $block->getOwner()->getSuper()->getId();
                // See if we need to position it below an inherited block
                $pos = array_search($block->getId(), $levels[$block->getOwner()->getId()], false);
                $prevBlockId = (isset($levels[$superId]) && isset($levels[$superId][$pos - 1])) ? $levels[$superId][$pos - 1] : null;
                if ($prevBlockId !== null) {
                    $prevBlockPos = array_search($prevBlockId, array_values($levels[$superId]));
                    $block->setSortParent(($prevBlockPos !== false) ? $prevBlockPos : -1);
                } else {
                    $block->setSortParent(-1);
                }
            } else {
                $block->setSortParent(-1);
            }
        }

        return $blocks;
    }

    /**
     * Sorts an array of Blocks using their $sort property taking into account inherited
     * BlockOwners.
     *
     * @param array $blocks
     *
     * @return array
     */
    public function sortBlocks(array $blocks)
    {
        // Perform a simple sort first
        usort($blocks, function ($a, $b)  {
            return ($a->getSort() < $b->getSort()) ? -1 : 1;
        });

        // Determine hierarchy of ownership first
        $ownerIds = array();
        foreach ($blocks as $block) {
            $superKey = ($block->getOwner()->getSuper()) ? array_search($block->getOwner()->getSuper()->getId(), $ownerIds) : false;
            if ($superKey === false) {
                array_unshift($ownerIds, $block->getOwner()->getId());
            } else {
                $ownerIds = array_merge(array_slice($ownerIds, 0, $superKey+1), array($block->getOwner()->getId()), array_slice($ownerIds, $superKey+1));
            }
        }
        $ownerIds = array_flip($ownerIds); // Flip it for easier lookup by parentId as key.
        asort($ownerIds);

        $sorted = array();
        $parentOwnerId = null;
        $parentSize = null;
        foreach ($ownerIds as $ownerId => $ownerPos) {
            // Get blocks in level/segment
            $segment = array_values(array_filter($blocks, function ($block) use ($ownerId) {
                return ($block->getOwner() && $block->getOwner()->getId() == $ownerId);
            }));

            if (! count($segment)) {
                $parentOwnerId = $ownerId;
                continue; // nothing to do for this owner
            }

            // now inject at correct positions
            if (count($segment) && $parentOwnerId === null) {
                $sorted = $segment;
            } else if (count($segment)) {
                $positioned = array_filter($segment, function ($block) {
                   return ($block->getSortParent() !== null && $block->getSortParent() >= 0);
                });

                // if nothing is positioned throw them on top
                if (! count($positioned)) {
                    $sorted = array_merge($segment, $sorted);
                } else {
                    // first part
                    $keys = array_keys($positioned);
                    if ($keys[0] > 0) {
                        $sorted = array_merge(array_slice($segment, 0, array_shift($keys)), $sorted);
                    } elseif ($keys[0] == 0) {
                        unset($keys[0]);
                    }

                    foreach ($positioned as $key => $block) {
                        $endIdx = (count($keys)) ? array_shift($keys) : null;
                        $insert = array_slice($segment, $key, ($endIdx) ? ($endIdx-$key) : null);

                        foreach ($sorted as $insertIdx => $item) {
                            // Check if we inherit from this block
                            if ($item->getOwner()->getId() == $parentOwnerId && $item->getSort() == $block->getSortParent() &&
                                // And check if we're in the same parent block on same position
                                (($item->getParent()->getId() == $block->getParent()->getId() || $item->isInRoot() && $block->isInRoot()) && $item->getPosition() == $block->getPosition())) {
                                break;
                            }
                        }
                        $sorted = array_merge(array_slice($sorted, 0, $insertIdx+1), $insert, array_slice($sorted, $insertIdx+1));
                    }
                }
            }

            $parentOwnerId = $ownerId;
        }

        return $sorted;
    }

    /**
     * @param mixed $block
     *
     * @return int
     */
    public function getNewVersion($block)
    {
        if (!is_object($block)) {
            $block = $this->find($block);
        }

        $version = ($block instanceof BlockOwnerInterface || $block->isShared()) ? $block->getVersion() : $block->getOwner()->getVersion();

        return $version + 1;
    }

    /**
     * Fixes nested tree hierarchy between parent and children by examining child's parent.
     *
     * Blocks may have altered parent/child relationships after reverting. Let's
     * loop and try to add the missing children to our tree.
     *
     * @param BlockContainerInterface    $block
     * @param \RecursiveIteratorIterator $iterator
     */
    public static function treeAddMissingChildren(BlockContainerInterface $block, \RecursiveIteratorIterator $iterator = null)
    {
        if (!$iterator) {
            $iterator = new \RecursiveIteratorIterator(
                new RecursiveBlockIterator(
                    ($block instanceof BlockOwnerInterface) ? $block->getOwning() : $block->getChildren()
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );
        }

        // Add children that belong
        $pass = array();
        foreach ($iterator as $object) {
            if (in_array($object->getId(), $pass)) {
                $iterator->next();
            }
            array_push($pass, $object->getId());

            // Found block that has current block as parent
            if ($object->getParent() && $object->getParent()->getId() == $block->getId()) {

                if (!$block->getChildren()->contains($object)) {
                    $block->addChild($object);
                }
            }
        }

        foreach ($block->getChildren() as $child) {
            if ($child instanceof BlockContainerInterface) {
                // Go deeper
                self::treeAddMissingChildren($child, $iterator);
            }
        }
    }

    /**
     * @param BlockContainerInterface $block
     */
    public static function treeRemoveInvalidChildren(BlockContainerInterface $block)
    {
        foreach ($block->getChildren() as $child) {
            if ($child instanceof BlockContainerInterface) {
                // Go deeper
                self::treeRemoveInvalidChildren($child);
            }

            // Remove children not belonging
            if ($child->getParent() && $child->getParent()->getId() !== $block->getId()) {
                $block->removeChild($child);
            }
        }
    }
}