<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Opifer\CmsBundle\EventListener\LoggableListener;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Entity\Template;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Repository\BlockLogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

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

    /** @var EntityManager */
    protected $em;

    const VERSION_PUBLISHED = "P";

    /**
     * Constructor
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->blocks = array();
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
     * @return array
     */
    public function getTools()
    {
        $tools = array();

        foreach ($this->services as $service) {
            if ($service instanceof ToolsetMemberInterface) {
                $tools[] = $service->getTool();
            }
        }

        return $tools;
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
     * With rootVersion set and version false it will give you the newest by default.
     *
     * @param integer      $id
     * @param null|integer $version
     *
     * @return BlockInterface
     */
    public function find($id, $version = null)
    {
        $block = $this->getRepository()->find($id);

        if ($version) {
            $this->revert($block, $version);
        }

        return $block;
    }

    /**
     * Find a Block in the repository with optional specified version.
     * With rootVersion set and version false it will give you the newest by default.
     *
     * @param integer      $id
     * @param null|integer $version
     *
     * @return BlockInterface
     */
    public function findById($id, $version = null)
    {
        $blocks = $this->getRepository()->findById($id);

        if ($version) {
            foreach ($blocks as $block) {
                $this->revert($block, $version);
            }
        }

        return $blocks;
    }

    /**
     * @param BlockOwnerInterface $owner
     * @param boolean             $version
     *
     * @return array
     */
    public function findByOwner(BlockInterface $owner, $version = false)
    {
        if ($version === self::VERSION_PUBLISHED && !$this->em->getFilters()->isEnabled('draftversion')) {
            $this->em->getFilters()->enable('draftversion');
        } elseif ($version != self::VERSION_PUBLISHED && $this->em->getFilters()->isEnabled('draftversion')) {
            $this->em->getFilters()->disable('draftversion');
        }


        $blocks = $this->getRepository()->findBy(['owner' => $owner], ['sort' => 'asc']);

        if ($version && $version !== self::VERSION_PUBLISHED) {
            foreach ($blocks as $key => $block) {
                $this->revertSingle($block, $version);

                if ($block->getDeletedAt() && $block->getDeletedAt() < new \DateTime) {
                    unset($blocks[$key]);
                }
            }
        }

        usort($blocks, function ($a, $b) {
            return ($a->getSort() < $b->getSort()) ? -1 : 1;
        });

        return $blocks;
    }

    /**
     * Publishes a block version
     *
     * @param BlockOwnerInterface $block
     * @param boolean|integer     $version
     */
    public function publish(BlockInterface $block)
    {
        if (!($block instanceof BlockOwnerInterface) && !$block->isShared()) {
            throw new \Exception ('Can only publish blocks of type BlockOwner or shared');
        }

        $this->killLoggableListener();
        $this->killSoftDeletableListener();

        $version = $this->getNewVersion($block);
        $owned = $this->findByOwner($block, $version);

        foreach ($owned as $descendant) {
            $descendant->setPublish(true);
            $descendant->setVersion($version);
            $descendant->setRootVersion($version);
            $this->save($descendant);
        }

        $this->revertSingle($block, $version);
        $block->setPublish(true);
        $block->setVersion($version);
        $block->setRootVersion($version);
        $this->save($block);
    }

    /**
     * Revert the block to the rootVersion and if version equals false it will give you the newest by default.
     *
     * @param BlockInterface $block
     * @param null|integer   $rootVersion
     */
    public function revert(BlockInterface $block, $version)
    {
        $block->accept(new RevertVisitor($this, $version));

        if ($block instanceof BlockContainerInterface) {
            self::treeAddMissingChildren($block);
            self::treeRemoveInvalidChildren($block);
            $block->accept(new SortVisitor());
        }
    }

    /**
     * Actual implementation of reverting a block to a state of the version.
     *
     * The method {@link revert()} can take a tree and uses this method on every
     * node to do the work.
     *
     * @param BlockInterface $block
     * @param integer        $rootVersion
     */
    public function revertSingle(BlockInterface $block, $version)
    {
        // check for LogEntries
        /** @var BlockLogEntryRepository $repo */
        $repo = $this->em->getRepository('OpiferContentBundle:BlockLogEntry');

        $logs = $repo->getLogEntriesRoot($block, $version);

        if (count($logs)) {
            $latest = current($logs)->getVersion();
            $repo->revert($block, $latest);  // We "revert" to the current working draft, as the Block itself is a published one
        } else {
            if ($block->getVersion() > $version) {
                // no logs where found, assume block was created in a later version
                $block->getParent()->removeChild($block);
                unset($block);
            }
        }
    }

    /**
     * Returns available (root) versions for this block
     *
     * @param BlockInterface $block
     *
     * @return ArrayCollection
     */
    public function getRootVersions(BlockInterface $block)
    {
        $rootId = ($block instanceof BlockOwnerInterface) ? $block->getId() : $block->getOwner()->getId();

        /** @var BlockLogEntryRepository $repo */
        $repo = $this->em->getRepository('OpiferContentBundle:BlockLogEntry');
        $versions = $repo->findDistinctByRootId($rootId);

        return $versions;
    }

    /**
     * @param BlockInterface $block
     *
     * @return BlockManager
     */
    public function save(BlockInterface $block)
    {
        $version = $this->getNewVersion($block);

        $block->setRootVersion($version);

        if (!$block->isPublish()) {
            // Reset LogEntry for the rare case the editted block ends up being the same as the published block
            // and nothing gets updated (not even the logentry because UnitOfWork does not schedule any updates)
            $this->em->getRepository('OpiferContentBundle:BlockLogEntry')->nullifyLogEntry($block, $version);
        }

        $this->em->persist($block);
        $this->em->flush($block);

        return $this;
    }

    /**
     * @param BlockInterface $block
     * @param null|integer   $rootVersion
     * @param boolean        $recursive
     */
    public function remove(BlockInterface $block, $recursive = false)
    {
        $this->killSoftDeletableListener();

        if (!$recursive) {
            $version = $this->getNewVersion($block);
            $block->setDeletedAt(new \DateTime());
            $block->setRootVersion($version);
            $this->em->flush($block);
        }
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
        $this->killDraftVersionFilter();
        $this->killLoggableListener();

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
            $descendant->setRootVersion(0);

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
     * @param integer $rootVersion
     */
    public function discardAll($ownerId)
    {
        $owner = $this->find($ownerId);
        if (!$owner instanceof BlockOwnerInterface) {
            throw new \Exception('Discard all changes is only possible on Block owners');
        }

        $this->killLoggableListener();
        $this->killSoftDeletableListener();

        $this->em->getRepository('OpiferContentBundle:BlockLogEntry')->discardAll($ownerId, $this->getNewVersion($owner));

        if ($this->em->getFilters()->isEnabled('softdeleteable')) {
            $this->em->getFilters()->disable('softdeleteable');
        }

        $stubs = $this->getRepository()->findBy(['owner' => $owner, 'version' => 0]);

        foreach ($stubs as $stub) {
            $this->em->remove($stub);
        }

        $this->em->flush();
    }

    /**
     * Kills the DraftVersionFilter
     */
    public function killDraftVersionFilter()
    {
        if ($this->em->getFilters()->isEnabled('draftversion')) {
            $this->em->getFilters()->disable('draftversion');
        }
    }

    /**
     * Removes the LoggableListener from the EventManager
     */
    public function killLoggableListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if ($listener instanceof LoggableListener) {
                    $listenerInst = $listener;
                    break 2;
                }
            }
        }

        $this->em->getEventManager()->removeEventListener(array('onFlush'), $listenerInst);
    }

    /**
     * Removes the SoftDeleteableListener from the EventManager
     */
    public function killSoftDeletableListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if ($listener instanceof SoftDeleteableListener) {
                    $listenerInst = $listener;
                    break 2;
                }
            }
        }

        $this->em->getEventManager()->removeEventListener(array('onFlush'), $listenerInst);
    }

    /**
     * TODO: refactor this
     *
     * @param integer      $ownerId
     * @param string       $type
     * @param integer      $parentId
     * @param integer      $placeholder
     * @param array        $sort
     * @param null|array   $data
     *
     * @throws \Exception
     *
     * @return BlockInterface
     */
    public function createBlock($ownerId, $type, $parentId, $placeholder, $sort, $data = null)
    {
        $owner = $this->find($ownerId);
        $version = $this->getNewVersion($owner);
        $className = $this->em->getClassMetadata($type)->getName();
        $block = new $className();
        $parent = $this->find($parentId ?: $ownerId);

        $block->setRootVersion($version);
        $block->setPosition($placeholder);
        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setSortParent(-1); // < need to be calculated when putting in the tree

        // Set owner
        $block->setOwner($owner);
        $owner->addOwning($block);
        $owner->setRootVersion($version);

        // This should replaced with a more hardened function
        if ($data) {
            foreach ($data as $attribute => $value) {
                $method = "set$attribute";
                $block->$method($value);
            }
        }

        $block->setParent($parent);
        $parent->setRootVersion($version);

        // Save now, rest will be in changeset. All we do is a create a stub entry anyway.
        $this->save($block);

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
            $contained = $this->findById($sort, $version);

            if ($contained) {
                $contained = $this->setSortsByDirective($contained, $sort);

                foreach ($contained as $node) {
                    if ($node->getOwner()->getId() == $block->getOwner()->getId()) {
                        $node->setRootVersion($version);
                        $this->save($node);
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
    public function moveBlock($id, $parentId, $placeholder, $sort)
    {
        $block = $this->find($id);
        $version = $this->getNewVersion($block);
        $block = $this->find($id, $version);

        $parent = ($parentId) ? $this->find($parentId) : $block->getOwner();

        $block->setPosition($placeholder);
        $block->setRootVersion($version);

        $block->setParent($parent);
        $parent->addChild($block);

        $this->save($block);

        array_walk($sort, function (&$id) { $id = (int) $id; });
        $contained = $this->findById($sort, $version);

        if ($contained) {
            $contained = $this->setSortsByDirective($contained, $sort);

            foreach ($contained as $node) {
                if ($node->getOwner()->getId() == $block->getOwner()->getId()) {
                    $node->setRootVersion($version);
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
        if ($this->em->getFilters()->isEnabled('draftversion')) {
            $this->em->getFilters()->disable('draftversion');
        }

        $block = $this->find($id);
        $version = $this->getNewVersion($block);

        // Duplicate some of the settings to the pointer
        $pointer = new PointerBlock();
        $pointer->setOwner($block->getOwner());
        $pointer->setParent($block->getParent());
        $pointer->setReference($block);
        $pointer->setRootVersion($version);

        // Detach and make shared
        $block->setShared(true);
        $block->setParent(null);
        $block->setOwner(null);
        $block->setPosition(0);
        $block->setSort(0);
        $block->setRootVersion($version);


        $this->killLoggableListener();

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
     * @param integer        $rootVersion
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


    public function revertRecursiveFromNode(BlockInterface $block, $rootVersion)
    {
        if (!$block->getOwner()) {
            $this->revert($block, $rootVersion);

            return $block;
        }

        $owner = $block->getOwner();
        $this->revert($owner, $rootVersion);

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
            if ($block instanceof BlockOwnerInterface) {
                $superKey = ($block->getSuper()) ? array_search($block->getSuper()->getId(), $ownerIds) : false;
                if ($superKey === false) {
                    array_unshift($ownerIds, $block->getId());
                } else {
                    $ownerIds = array_merge(array_slice($ownerIds, 0, $superKey+1), array($block->getId()), array_slice($ownerIds, $superKey+1));
                }
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