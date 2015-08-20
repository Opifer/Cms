<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Block\BlockServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Repository\BlockLogEntryRepository;

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
     * @param null|integer $rootVersion
     *
     * @return BlockInterface
     */
    public function find($id, $rootVersion = null)
    {
        /** @var BlockInterface $block */
        $block = $this->getRepository()->find($id);

        if ($rootVersion) {
            $this->revert($block, $rootVersion);
        }

        return $block;
    }

    /**
     * Publishes a block version
     *
     * @param BlockInterface $block
     * @param null|integer   $rootVersion
     */
    public function publish(BlockInterface $block, $rootVersion)
    {
        if ($block instanceof BlockContainerInterface) {
            $iterator = new \RecursiveIteratorIterator(
                new RecursiveBlockIterator(
                    $block->getChildren()
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $descendant) {
                $this->revertSingle($descendant, $rootVersion);
                $descendant->setPublish(true);
                $descendant->setVersion($rootVersion);
                $this->save($descendant);
            }
        }

        $this->revertSingle($block, $rootVersion);
        $block->setPublish(true);
        $block->setVersion($rootVersion);
        $this->save($block);
    }

    /**
     * Revert the block to the rootVersion and if version equals false it will give you the newest by default.
     *
     * @param BlockInterface $block
     * @param null|integer   $rootVersion
     */
    public function revert(BlockInterface $block, $rootVersion)
    {
        if ($block instanceof BlockContainerInterface) {
            $iterator = new \RecursiveIteratorIterator(
                new RecursiveBlockIterator(
                    ($block instanceof BlockOwnerInterface) ? $block->getOwning() : $block->getChildren()
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            $owning = new ArrayCollection();
            foreach ($iterator as $descendant) {
                $this->revertSingle($descendant, $rootVersion);
                $owning->add($descendant);
            }
        }

        $this->revertSingle($block, $rootVersion);

        if ($block instanceof BlockContainerInterface) {
            $block->setOwning($owning);
            $block->setChildren($owning);
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
    protected function revertSingle(BlockInterface $block, $rootVersion)
    {
        // check for LogEntries
        /** @var BlockLogEntryRepository $repo */
        $repo = $this->em->getRepository('OpiferContentBundle:BlockLogEntry');

        $logs = $repo->getLogEntriesRoot($block, $rootVersion);

        if (count($logs)) {
            $latest = current($logs)->getVersion();
            $repo->revert($block, $latest);  // We "revert" to the current working draft, as the Block itself is a published one
        } else {
            // no logs where found, assume block was created in a later version
            $block->getParent()->removeChild($block);
            unset($block);
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
     * @param null|integer   $rootVersion
     * @param boolean        $recursive
     */
    public function save(BlockInterface $block, $rootVersion = null, $recursive = false)
    {
        if (!$recursive) {
            if ($rootVersion) {
                $block->setRootVersion($rootVersion);
            }

            $this->em->persist($block);
            $this->em->flush();
        }
    }

    /**
     * @param BlockInterface $block
     * @param null|integer   $rootVersion
     * @param boolean        $recursive
     */
    public function remove(BlockInterface $block, $rootVersion = null, $recursive = false)
    {
        if (!$recursive) {
            $block->setRootVersion($rootVersion);

            $this->em->remove($block);
            $this->em->flush();
        }
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
     * @param null|integer $rootVersion
     *
     * @throws \Exception
     *
     * @return BlockInterface
     */
    public function createBlock($ownerId, $type, $parentId, $placeholder, $sort, $data = null, $rootVersion = null)
    {
        $className = $this->em->getClassMetadata($type)->getName();
        $block = new $className();
        $block->setRootVersion($rootVersion);
        $block->setPosition($placeholder);
        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setLevel(0); // < need to be calculated when putting in the tree

        // Set owner
        $owner = $this->find($ownerId, $rootVersion);
        $block->setOwner($owner);
        $owner->addOwning($block);
        $owner->setRootVersion($rootVersion);

        // This should replaced with a more hardened function
        if ($data) {
            foreach ($data as $attribute => $value) {
                $method = "set$attribute";
                $block->$method($value);
            }
        }

        // Do we need to set a parent at this time? The changeset LogEntry will hold all data probably.
        if (!$parentId) {
            $parentId = $ownerId;
        }
        $parent = $this->find($parentId, $rootVersion);
        $block->setParent($parent);
        $parent->setRootVersion($rootVersion);

        // Save now, rest will be in changeset. All we do is a create a stub entry anyway.
        $this->save($block);

        if (count($sort) > 1) {
            $value = "";
            $id = $block->getId();
            array_map(
                function ($v) use ($value, $id) {
                    return $v == $value ? $id : $v;
                },
                $sort
            );

            $siblings = $this->getSiblings($block, $rootVersion);
            if ($siblings) {
                $siblings = $this->sortBlocksByIds($siblings, $sort);

                foreach ($siblings as $sibling) {
                    $this->save($sibling);
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
     * @param integer $rootVersion
     */
    public function moveBlock($id, $parentId, $placeholder, $sort, $rootVersion)
    {
        $block = $this->find($id, $rootVersion);

        $block->setPosition($placeholder);
        $block->setRootVersion($rootVersion);

        if (!$parentId) {
            $parent = $block->getOwner();
        } else {
            $parent = $this->find($parentId, $rootVersion);
        }
        $block->setParent($parent);
        $parent->addChild($block);

        $this->save($block);

        $siblings = $this->getSiblings($block, $rootVersion);
        if ($siblings) {
            // kick siblings not in this placeholder
            foreach ($siblings as $sibling) {
                if ($sibling->getPosition() != $placeholder) {
                    $siblings->removeElement($sibling);
                }
            }
            $siblings = $this->sortBlocksByIds($siblings, $sort);

            foreach ($siblings as $sibling) {
                $this->save($sibling);
            }
        }
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
    public function getSiblings(BlockInterface $block, $rootVersion)
    {
        $owner = $block->getOwner();
        $this->revert($owner, $rootVersion);

        $iterator = new \RecursiveIteratorIterator(
            new RecursiveBlockIterator(
                $owner->getChildren()
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->getParent() && $item->getParent()->getId() == $block->getParent()->getId()) {
                return $item->getParent()->getChildren();
            }
        }

        return false;
    }

    /**
     * @param array   $blocks
     * @param array   $sort
     *
     * @return array
     */
    public function sortBlocksByIds($blocks, $sort)
    {
        $array = $blocks->getValues();

        array_walk($array, function ($block) use ($sort) {
            $block->setSort(array_search($block->getId(), $sort));
        });

        return $array;
    }

    /**
     * @param BlockInterface $block
     *
     * @return int
     */
    public function getNewVersion(BlockInterface $block)
    {
        return $block->getVersion() + 1;
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