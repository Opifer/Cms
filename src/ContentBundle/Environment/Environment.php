<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 21/01/16
 * Time: 15:29
 */

namespace Opifer\ContentBundle\Environment;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Model\BlockInterface;

abstract class Environment
{
    protected $isLoaded = false;

    /**
     * @var array
     */
    protected $versionMap;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $blockMode;

    /**
     * @var BlockManager
     */
    protected $blockManager;

    protected $blockCache;

    public function __construct(EntityManagerInterface $em, BlockManager $blockManager)
    {
        $this->em = $em;
        $this->blockManager = $blockManager;
    }

    /**
     * Loads main entity
     *
     * @return array
     */
    abstract public function load($id);

    /**
     * Return all block owners to assist block loading.
     *
     * @return array
     */
    abstract protected function getBlockOwners();

    /**
     * Return the main block of the environment
     *
     * @return BlockOwnerInterface
     */
    abstract public function getMainBlock();

    /**
     * Returns view parameters array that can be passed to any Response
     *
     * @return array
     */
    public function getViewParameters() {
        return array(
            'environment' => $this,
            'block_mode' => $this->getBlockMode(),
        );
    }

    /**
     * @return array
     */
    public function getVersionMap()
    {
        return $this->versionMap;
    }

    /**
     * @param array $versionMap
     */
    public function setVersionMap($versionMap)
    {
        $this->versionMap = $versionMap;
    }

    /**
     * @return string
     */
    public function getBlockMode()
    {
        return $this->blockMode;
    }

    /**
     * @param string $blockMode
     */
    public function setBlockMode($blockMode)
    {
        $this->blockMode = $blockMode;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return 'base.html.twig';
    }


    public function getBlock($id)
    {
        $this->loadBlocks();

        $cacheKey = $this->getCacheKey();

        foreach ($this->blockCache[$cacheKey] as $member) {
            if ($member->getId() == $id) {
                return $member;
            }
        }

        throw new \Exception('Could not find block in loaded Environment');
    }

    public function getRootBlocks()
    {
        $this->loadBlocks();

        $cacheKey = $this->getCacheKey();

        $blocks = array();
        $blockOwners = $this->getBlockOwners();

        foreach ($this->blockCache[$cacheKey] as $member) {
            foreach ($blockOwners as $blockOwner) {
                if ($member->getParent() && $member->getParent()->getId() == $blockOwner->getId()) {
                    array_push($blocks, $member);
                }
            }
        }

        return $blocks;
    }

    /**
     *
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getBlockChildren(BlockInterface $block)
    {
        $this->loadBlocks();

        $children = array();
        $cacheKey = $this->getCacheKey();

        foreach ($this->blockCache[$cacheKey] as $member) {
            if ($member->getParent() == null)
                continue;

            if ($member->getParent()->getId() == $block->getId()) { // direct child
                array_push($children, $member);
            } else if ($member->getOwner() && $member->getParent()->getId() == $member->getOwner()->getId() && $block instanceof BlockOwnerInterface) {
                array_push($children, $member);
            }
        }

        return $children;
    }

    /**
     * Loads all blocks needed to display the document for caching purposes. This
     * includes loading blocks that are owned by the Template.
     *
     * @return Environment
     */
    protected function loadBlocks()
    {
        if ($this->isLoaded)
            return;

        $blockOwners = $this->getBlockOwners();
        $blocks = array();

        foreach ($blockOwners as $blockOwner) {
            $version = $this->getVersion($blockOwner->getId());

            $owned = $this->blockManager->findByOwner($blockOwner, $version);

            $blocks = array_merge($blocks, $owned);
        }

        $blocks = $this->blockManager->sortBlocks(array_merge($blocks, $blockOwners));
        $cacheKey = $this->getCacheKey();

        $this->blockCache[$cacheKey] = array_filter($blocks, function($item) {
            return ($item instanceof DocumentBlock) ? false : true;
        });

        $this->isLoaded = true;

        return $this;
    }

    protected function getCacheKey()
    {
        $id = $this->getMainBlock()->getId();
        $version = $this->getVersion($id);
        $cacheKey = ($version) ? sprintf('%d-%d', $id, $version) : $id;

        return $cacheKey;
    }

    /**
     * @param integer $id
     * @param integer $version
     *
     * return integer
     */
    public function setVersion($version, $id = false)
    {
        if ($id === false) {
            $id = $this->getMainBlock()->getId();
        }

        $this->versionMap[$id] = $version;
    }

    public function getVersion($id)
    {
        return ($this->versionMap && isset($this->versionMap[$id])) ? $this->versionMap[$id] : BlockManager::VERSION_PUBLISHED;
    }
}