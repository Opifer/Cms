<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Opifer\CmsBundle\EventListener\LoggableListener;
use Opifer\ContentBundle\Block\BlockServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Template;
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
     * @param null|integer $rootVersion
     *
     * @return BlockInterface
     */
    public function find($id, $rootVersion = null, $recursive = false)
    {
        /** @var BlockInterface $block */
        $block = $this->getRepository()->find($id);

        if ($rootVersion) {
            if (!$recursive) {
                $this->revert($block, $rootVersion);
            } else {
                return $this->revertRecursiveFromNode($block, $rootVersion);
            }
        }

        return $block;
    }

    /**
     * @param BlockOwnerInterface $owner
     * @param mixed               $rootVersion
     *
     * @return array
     */
    public function findByOwner(BlockOwnerInterface $owner, $rootVersion = null)
    {
        $blocks = $this->getRepository()->findBy(['owner' => $owner], ['sort' => 'asc']);

        if ($rootVersion) {
            foreach ($blocks as $key => $block) {
                $this->revert($block, $rootVersion);

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
     * @param null|integer        $rootVersion
     */
    public function publish(BlockOwnerInterface $block, $rootVersion)
    {
        $this->killLoggableListener();
        $this->killSoftDeletableListener();

        $owned = $this->findByOwner($block);

        foreach ($owned as $descendant) {
            $this->revertSingle($descendant, $rootVersion);
            $descendant->setPublish(true);
            $descendant->setVersion($rootVersion);
            $descendant->setRootVersion($rootVersion);
            $this->save($descendant);
        }

        $this->revertSingle($block, $rootVersion);
        $block->setPublish(true);
        $block->setVersion($rootVersion);
        $block->setRootVersion($rootVersion);
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
        $block->accept(new RevertVisitor($this, $rootVersion));

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
    public function revertSingle(BlockInterface $block, $rootVersion)
    {
        // check for LogEntries
        /** @var BlockLogEntryRepository $repo */
        $repo = $this->em->getRepository('OpiferContentBundle:BlockLogEntry');

        $logs = $repo->getLogEntriesRoot($block, $rootVersion);

        if (count($logs)) {
            $latest = current($logs)->getVersion();
            $repo->revert($block, $latest);  // We "revert" to the current working draft, as the Block itself is a published one
        } else {
            if ($block->getVersion() > $rootVersion) {
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
     * @param null|integer   $rootVersion
     * @param boolean        $recursive
     */
    public function save(BlockInterface $block, $rootVersion = null, $recursive = false)
    {
        if (!$recursive) {
            if ($rootVersion) {
                $block->setRootVersion((int) $rootVersion);
            }

            $this->em->persist($block);
            $this->em->flush($block);
        }
    }

    /**
     * @param BlockInterface $block
     * @param null|integer   $rootVersion
     * @param boolean        $recursive
     */
    public function remove(BlockInterface $block, $rootVersion = null, $recursive = false)
    {
        $this->killSoftDeletableListener();

        if (!$recursive) {
            if ($rootVersion) {
                $block->setRootVersion($rootVersion);
            }

            $block->setDeletedAt(new \DateTime());

//            $this->em->remove($block);
            $this->em->flush();
        }
    }

    /**
     * @param integer $ownerId
     * @param integer $rootVersion
     */
    public function discardAll($ownerId, $rootVersion)
    {
        $owner = $this->find($ownerId, $rootVersion);
        if (!$owner instanceof BlockOwnerInterface) {
            throw new \Exception('Discard all changes is only possible on Block owners');
        }

        $this->killLoggableListener();
        $this->killSoftDeletableListener();

        $this->em->getRepository('OpiferContentBundle:BlockLogEntry')->discardAll($ownerId, $rootVersion);

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
     * @param null|integer $rootVersion
     *
     * @throws \Exception
     *
     * @return BlockInterface
     */
    public function createBlock($ownerId, $type, $parentId, $placeholder, $sort, $data = null, $rootVersion = null)
    {
        $owner = $this->find($ownerId, $rootVersion);
        $className = $this->em->getClassMetadata($type)->getName();
        $block = new $className();
        $parent = $this->find($parentId ?: $ownerId, $rootVersion);

        $block->setRootVersion($rootVersion);
        $block->setPosition($placeholder);
        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setLevel(0); // < need to be calculated when putting in the tree

        // Set owner
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

        $block->setParent($parent);
        $parent->setRootVersion($rootVersion);

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

            $siblings = $this->getSiblings($block, $rootVersion);
            $siblings[] = $block;
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
        $parent = ($parentId) ? $this->find($parentId, $rootVersion) : $block->getOwner();

        $block->setPosition($placeholder);
        $block->setRootVersion($rootVersion);

        $block->setParent($parent);
        $parent->addChild($block);

        $this->save($block);

        $siblings = $this->getSiblings($block, $rootVersion);

        if ($siblings) {
            // kick siblings not in this placeholder
            foreach ($siblings as $k => $sibling) {
                if ($sibling->getPosition() != $placeholder) {
                    unset($siblings[$k]);
                }
            }
            $siblings = $this->sortBlocksByIds($siblings, $sort);

            foreach ($siblings as $sibling) {
                $sibling->setRootVersion($rootVersion);
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
        $family = $this->findByOwner($owner, $rootVersion);

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
     * @param array   $blocks
     * @param array   $sort
     *
     * @return array
     */
    public function sortBlocksByIds($blocks, $sort)
    {
//        $array = $blocks->getValues();

        array_walk($blocks, function ($block) use ($sort) {
            $block->setSort(array_search($block->getId(), $sort));
        });

        return $blocks;
    }

    /**
     * @param BlockInterface $block
     *
     * @return int
     */
    public function getNewVersion(BlockInterface $block)
    {
        $version = ($block instanceof BlockOwnerInterface) ? $block->getVersion() : $block->getOwner()->getVersion();

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