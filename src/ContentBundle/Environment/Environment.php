<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 21/01/16
 * Time: 15:29
 */

namespace Opifer\ContentBundle\Environment;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Block\Service\LayoutBlockServiceInterface;
use Opifer\ContentBundle\Entity\Template;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\TemplatedInterface;
use Opifer\ContentBundle\Provider\BlockProviderPool;
use Opifer\Revisions\Exception\DeletedException;
use Opifer\Revisions\RevisionManager;

class Environment
{
    protected $isLoaded = false;

    /**
     * @var object
     */
    protected $object;


    protected $type;

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

    /**
     * @var RevisionManager
     */
    protected $revisionManager;

    /**
     * @var TwigAnalyzer
     */
    protected $twigAnalyzer;

    protected $blockCache;

    /** @var bool */
    protected $draft = false;

    public function __construct(EntityManagerInterface $em, BlockManager $blockManager, RevisionManager $revisionManager, TwigAnalyzer $twigAnalyzer)
    {
        $this->em = $em;
        $this->blockManager = $blockManager;
        $this->revisionManager = $revisionManager;
        $this->twigAnalyzer = $twigAnalyzer;
    }


    /**
     * Returns view parameters array that can be passed to any Response
     *
     * @return array
     */
    public function getViewParameters()
    {
        return array(
            'environment' => $this,
            'block_mode' => $this->getBlockMode(),
        );
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
        if (method_exists($this->object, 'getView')) {
            return $this->object->getView();
        } elseif ($this->object instanceof TemplatedInterface) {
            return $this->object->getTemplate()->getView();
        }

        return 'base.html.twig';
    }

    public function getBlock($id)
    {
        $this->load();

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
        $this->load();

        $cacheKey = $this->getCacheKey();

        $blocks = array();

        /** @var BlockInterface $block */
        foreach ($this->blockCache[$cacheKey] as $block) {
            if ($block->getParent() === null) {
                array_push($blocks, $block);
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
        $this->load();

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
    public function load()
    {
        if ($this->isLoaded)
            return;

        $blocks = $this->getBlocksRecursive($this->object);

        $blocks = $this->blockManager->sortBlocks($blocks);
        $cacheKey = $this->getCacheKey();

        $this->blockCache[$cacheKey] = $blocks;

        $this->isLoaded = true;

        return $this;
    }

    protected function getBlocksRecursive($owner = null, $blocks = array())
    {
        $draft = ($this->draft && $owner === $this->object) ? true : false;

        $owned = $this->blockManager->findByOwner($owner, $draft);

        if ($owned instanceof PersistentCollection) {
            $owned = $owned->getValues();
        }

        $blocks = array_merge($blocks, $owned);

        if ($owner instanceof TemplatedInterface && $owner->getTemplate()) {
            $blocks = $this->getBlocksRecursive($owner->getTemplate(), $blocks);
        }

        return $blocks;
    }

    protected function isTemplated($object)
    {
        return ($object instanceof TemplatedInterface);
    }

    protected function getCacheKey()
    {
        $cacheKey = sprintf('%s-%d-%s', get_class($this->object), $this->object->getId(), $this->draft ? 'D' : 'L');

        return $cacheKey;
    }

    public function getTool(BlockInterface $block) {
        return $this->getService($block)->getTool();
    }

    public function getService(BlockInterface $block) {
        return $this->blockManager->getService($block);
    }

    public function getPlaceholders($object)
    {
        if ($object instanceof Content || $object instanceof Template) {
            $view = $this->getView();
        } else {
            $service = $this->blockManager->getService($object);

            if ($service instanceof LayoutBlockServiceInterface) {
                return $service->getPlaceholders($object);
            }

            $view = $service->getView($object);
        }

        return $this->twigAnalyzer->findPlaceholders($view);
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param object $object
     *
     * @return Environment
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * @param boolean $draft
     * @return Environment
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;

        return $this;
    }


}