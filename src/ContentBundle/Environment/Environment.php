<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 21/01/16
 * Time: 15:29
 */

namespace Opifer\ContentBundle\Environment;

use Opifer\ContentBundle\Model\BlockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Block\BlockManager;

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
    abstract public function load($id, $version = false);

    /**
     * Return all block owners to assist block loading.
     *
     * @return array
     */
    abstract protected function getBlockOwners();

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
     * @param BlockInterface $block
     *
     * @return string
     */
    public function getBlockMode(BlockInterface $block = null)
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

        $blockOwners = $this->getBlockOwners();

        foreach ($blockOwners as $blockOwner) {
            $cacheKey = $this->getCacheKey($blockOwner->getId());

            foreach ($this->blockCache[$cacheKey] as $member) {
                if ($member->getId() == $id) {
                    return $member;
                }
            }
        }

        throw new \Exception('Could not find block in loaded Environment');
    }

    public function getRootBlocks()
    {
        $this->loadBlocks();

        $blocks = array();
        $blockOwners = $this->getBlockOwners();

        foreach ($blockOwners as $blockOwner) {
            $cacheKey = $this->getCacheKey($blockOwner->getId());

            foreach ($this->blockCache[$cacheKey] as $member) {
                if ($member->getParent()->getId() == $blockOwner->getId()) {
                    array_push($blocks, $member);
                }
            }
        }

        return $blocks;
    }

    /**
     *
     * @param BlockInterface $block
     * @param mixed          $versions Array map with block owner id and version number
     *
     * @return array
     */
    public function getBlockChildren(BlockInterface $block)
    {
        $this->loadBlocks();

        $blockOwners = $this->getBlockOwners();

        $children = array();
        foreach ($blockOwners as $blockOwner) {
            $cacheKey = $this->getCacheKey($blockOwner->getId());

            foreach ($this->blockCache[$cacheKey] as $member) {
                if ($member->getParent()->getId() == $block->getId() || ($member->getParent()->getId() == $member->getOwner()->getId() && $block instanceof BlockOwnerInterface)) {
                    array_push($children, $member);
                }
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

        foreach ($blockOwners as $blockOwner) {
            $version = $this->getVersion($blockOwner->getId());
            $cacheKey = $this->getCacheKey($blockOwner->getId());

            if (!isset($this->blockCache[$cacheKey])) {
                $this->blockCache[$cacheKey] = $this->blockManager->findByOwner($blockOwner, $version);
            }
        }

        $this->isLoaded = true;

        return $this;
    }

    protected function getCacheKey($id)
    {
        $version = $this->getVersion($id);
        $cacheKey = ($version) ? sprintf('%d-%d', $id, $version) : $id;

        return $cacheKey;
    }

    protected function getVersion($id)
    {
        return ($this->versionMap && isset($this->versionMap[$id])) ? $this->versionMap[$id] : false;
    }
}