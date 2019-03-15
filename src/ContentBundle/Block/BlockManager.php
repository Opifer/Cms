<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Gedmo\Timestampable\TimestampableListener;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Toolset;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Repository\BlockLogEntryRepository;
use Opifer\Revisions\EventListener\RevisionListener;
use Opifer\Revisions\Exception\DeletedException;
use Opifer\Revisions\RevisionManager;

/**
 * Block Manager
 *
 * This class provides methods mainly for managing blocks inside of the editor at a specific
 * version. It takes care of applying the changeset from BlockLogEntry to create a real-time
 * state of the Block instance before publishing/persisting it.
 */
class BlockManager
{
    /** @var array */
    protected $services;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var RevisionManager */
    protected $revisionManager;

    /** @var RevisionListener */
    protected $revisionListener = null;

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
                $tool = $service->getTool(null);
                if (is_array($tool)) {
                    $toolbelt->addTools($tool);
                } else {
                    $toolbelt->addTool($tool);
                }
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
        if ($draft) {
            $this->setDraftVersionFilter(! $draft);
        }

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

        $blocks = $this->getRepository()->findByOwner($owner);

//        $blocks = $owner->getBlocks();

        if ($draft) {
            $this->revertToDraft($blocks);
        }

        return $blocks;
    }

    /**
     * Finds the block and all its children recursively
     *
     * @param BlockInterface $parent
     * @param bool $draft
     * @return BlockInterface[]
     */
    public function findDescendants($parent, $draft = true)
    {
        $this->setDraftVersionFilter(! $draft);
        
        $iterator = new \RecursiveIteratorIterator(
            new RecursiveBlockIterator([$parent]),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $blocks = [];
        foreach ($iterator as $descendant) {
            $blocks[] = $descendant;
        }

        if ($draft) {
            $this->revertToDraft($blocks);
        }

        return $blocks;
    }

    public function revertToDraft($blocks)
    {
        $single = ! is_array($blocks) && $blocks instanceof BlockInterface;

        if ($single) {
            $blocks = array($blocks);
        }

        foreach ($blocks as $key => $block) {
            $currentRevision = $this->revisionManager->getCurrentRevision($block);
            $latestRevision = $this->revisionManager->getLatestRevision($block);
            if ($latestRevision !== false && $currentRevision < $latestRevision) {
                try {
                    $this->revisionManager->revert($block, $latestRevision);
                    $block->setDraft(true);
                } catch (DeletedException $e) {
                    unset($blocks[$key]);
                }
            }
        }

        return ($single) ? array_shift($blocks) : $blocks;
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
     * @param BlockInterface|BlockInterface[]|array $blocks
     */
    public function publish($blocks)
    {
        if ($blocks instanceof PersistentCollection) {
            $blocks = $blocks->getValues();
        }

        if (!$blocks ||
            (is_array($blocks) && !count($blocks))) {
            return;
        }

        if (! is_array($blocks)) {
            $blocks = [$blocks];
        }

        $this->disableRevisionListener();

        $deletes = [];

        foreach ($blocks as $block) {
            if (null !== $revision = $this->revisionManager->getDraftRevision($block)) {
                try {
                    $this->revisionManager->revert($block, $revision);
                } catch (DeletedException $e) {
                    // $this->em->remove($block);
                    $deletes[] = $block;
                }
            }
        }

        $this->em->flush();

        // Cycle through all deleted blocks to perform cascades manually 
        foreach ($deletes as $block) {
            if ($block instanceof CompositeBlock) {
                $descendants = $this->findDescendants($block, false);
            } else {
                $descendants = [$block];
            }

            foreach ($descendants as $descendant) {
                $descendant->setDeletedAt(new \DateTime);
                $this->em->flush($descendant);
            }
        }

        $cacheDriver = $this->em->getConfiguration()->getResultCacheImpl();
        $cacheDriver->deleteAll();

        $this->enableRevisionListener();
    }

    /**
     * @param BlockInterface $block
     *
     * @return BlockManager
     */
    public function save(BlockInterface $block, $draft = true)
    {
        $this->setDraftVersionFilter(! $draft);
        $block->setDraft($draft);

        $this->em->persist($block);
        $this->em->flush($block);

        return $this;
    }


    /**
     * @param BlockInterface $block
     */
    public function remove(BlockInterface $block, $draft = true)
    {
        $block->setDraft($draft);

        $this->em->remove($block);
        $this->em->flush($block);
    }

    /**
     * Clones blocks and persists to database
     *
     * @param BlockInterface      $block
     * @param BlockOwnerInterface $owner
     *
     * @return BlockInterface
     */
    public function duplicate($blocks, $owner = null)
    {
        if ($blocks instanceof BlockInterface) {
            $blocks = array($blocks);
        }

        $iterator = new \RecursiveIteratorIterator(
            new RecursiveBlockIterator($blocks),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $originalIdMap = array();
        $originalParentMap = array();

        $clones = array();

        // iterate over all owned blocks and disconnect parents keeping ids
        /** @var Block $block */
        foreach ($iterator as $block) {
            $blockId = $block->getId();
            $parentId = false;

            if (in_array($block->getId(), $originalIdMap)) {
                continue;
            }

            $clone = clone $block;
            $clone->setId(null);
            $clone->setParent(null);
            $clone->setOwner($owner);

            // if it has a parent we need to keep the id as reference for later
            if ($block->getParent()) {
                $parentId = $block->getParent()->getId();
            }

            if ($clone instanceof BlockContainerInterface) {
                $clone->setChildren(null);
            }

            $this->em->persist($clone);
            $this->em->flush($clone); // the block gets a new id

            $originalIdMap[$clone->getId()] = $blockId;
            if ($parentId) {
                $originalParentMap[$clone->getId()] = $parentId;
            }

            $clones[] = $clone;
        }

        // iterate over all new blocks and reset their parents
        foreach ($clones as $clone) {
            if (isset($originalParentMap[$clone->getId()])) {
                foreach ($clones as $parent) {
                    if (isset($originalParentMap[$clone->getId()]) &&
                        $originalParentMap[$clone->getId()] === $originalIdMap[$parent->getId()]) {
                        $clone->setParent($parent);
                        $parent->addChild($clone);

                        $this->em->flush($clone);
                        $this->em->flush($parent);
                    }
                }
            }
        }

        return $clones;
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
     * Removes the RevisionListener from the EventManager
     */
    public function disableRevisionListener()
    {
        foreach ($this->em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if ($listener instanceof RevisionListener) {
                    $this->revisionListener = $listener;
                    break 2;
                }
            }
        }

        if ($this->revisionListener) {
            $this->revisionListener->setActive(false);
        }
    }

    /**
     * Adds the RevisionListener back to the EventManager
     */
    public function enableRevisionListener()
    {
        if (! $this->revisionListener) {
            throw new \Exception('Could not enable revision listener: instance not found');
        }

        $this->revisionListener->setActive(true);
    }

    /**
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
        /** @var AbstractBlockService $service */
        $service = $this->getService($type);
        $parent = $parentId ? $this->find($parentId, $draft) : null;

        // This should replaced with a more hardened function
        if (is_null($data)) {
            $data = array();
        }
        $data['owner'] = $owner;

        $block = $service->createBlock($data);

        $block->setPosition($placeholder);
        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setSortParent(-1); // < need to be calculated when putting in the tree

        // Set owner
        if (is_null($parent) || false === $parent->isShared()) {
            $block->setOwner($owner);
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
                    // If nodes are in the same parent and if their owners match we
                    // save the changes. We don't want to save sort changes made to
                    // nodes belonging to inherited trees.
                    if ($node->getParent() === $block->getParent() &&
                        (($node->getOwner() === null && $block->getOwner() === null) ||
                        ($node->getOwner()->getId() == $block->getOwner()->getId()))) {
                        $this->save($node, $draft);
                    }
                }
            }
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
     *
     * @return BlockPointer
     */
    public function makeBlockShared($id)
    {
        $block = $this->find($id, true);
        $this->publish($block);

        // Duplicate some of the settings to the pointer
        $pointer = new PointerBlock();
        $pointer->setOwner($block->getOwner());
        $pointer->setParent($block->getParent());
        $pointer->setPosition($block->getPosition());
        $pointer->setSort($block->getSort());
        $pointer->setReference($block);
        $pointer->setDraft(true);

        // Detach and make shared
        $block->setShared(true);
        $block->setSharedName(sprintf('%s_%s', $block->getBlockType(), $block->getOwner()->getId()));
        $block->setSharedDisplayName(sprintf('%s from %s', $block->getBlockType(), $block->getOwnerName()));
        $block->setParent(null);
        $block->setOwner(null);
        $block->setPosition(null);
        $block->setSort(null);

        $this->save($block, false)->save($pointer);

        if ($block instanceof CompositeBlock) {
            $iterator = new \RecursiveIteratorIterator(
                new RecursiveBlockIterator($block->getChildren()),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $child) {
                $child->setOwner(null);
                $this->publish($child);
                $this->save($child, false);
            }
        }

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
     * @param integer|bool   $version
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
            $ownerId = ($block->getOwner()) ? $block->getOwner()->getId() : 0;
            $levels[$ownerId][$pos] = $block->getId();
        }

        foreach ($levels as &$level) {
            ksort($level);
        }

        foreach ($blocks as $block) {
            $ownerId = ($block->getOwner()) ? $block->getOwner()->getId() : 0;
            $sortedIds = $levels[$ownerId];
            $pos = array_search(array_search($block->getId(), $sortedIds), array_keys($sortedIds));
            $block->setSort($pos);

            if ($block->getOwner() && $block->getOwner()->getSuper()) {
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
     * owners.
     *
     * @param array $blocks
     *
     * @return array
     */
    public function sortBlocks(array $blocks)
    {
        // Perform a simple sort first
        usort($blocks, function ($a, $b)  {
            if ($a->getSort() == $b->getSort()) {
                return 0;
            }
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
                                (($item->isInRoot() && $block->isInRoot() || ($item->getParent() && $block->getParent() && $item->getParent()->getId() == $block->getParent()->getId())) && $item->getPosition() == $block->getPosition())) {
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
}
