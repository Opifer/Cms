<?php

namespace Opifer\ContentBundle\Block;

use Aws\CloudFront\Exception\Exception;
use Opifer\CmsBundle\Entity\Content;
use Opifer\ContentBundle\Block\BlockServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Opifer\ContentBundle\Entity\Template;
use Opifer\ContentBundle\Entity\LayoutBlock;
use Opifer\ContentBundle\Entity\ContentBlock;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class BlockManager
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
        $block = $this->getRepository()->find($id);

        return $this->getService($block);
    }

    /**
     * Returns the block service
     *
     * @param mixed $block
     *
     * @throws Exception if the blockservice can not be found
     *
     * @return BlockServiceInterface
     */
    public function getService($block)
    {
        $blockType = ($block instanceof BlockInterface) ? $block->getType() : $block;
        if (!isset($this->services[$blockType])) {
            throw new \Exception(sprintf("No BlockService available by the alias %s for block %s, available: %s", $blockType, get_class($block), implode(', ', array_keys($this->services))));
        }

        return $this->services[$blockType];
    }

    /**
     * @param BlockInterface $block
     * @param boolean        $recursive
     */
    public function save(BlockInterface $block, $recursive = false)
    {
        if (!$recursive) {
            $this->em->persist($block);
            $this->em->flush();
        }
    }

    /**
     * @param BlockInterface $block
     * @param boolean        $recursive
     */
    public function remove(BlockInterface $block, $recursive = false)
    {
        if (!$recursive) {
            $this->em->remove($block);
            $this->em->flush();
        }
    }

    /**
     * @param array $blocks
     * @param array $sort
     */
    public function reSortBlocksByIds($blocks, $sort)
    {
        $array = $blocks->getValues();
        array_walk($array, function ($block) use ($sort) {
            $block->setSort(array_search($block->getId(), $sort));
        });

        return $blocks;
    }


    /**
     * @todo: Create fixture from this method later
     */
    public function createDemoBlocks()
    {
        $template = new Template;
        $template->setLevel(0);
        $template->setSort(0);
        $template->setName('city');
        $template->setDisplayName('City');

        $this->em->persist($template);

        $header = new LayoutBlock;
        $header->setType('one_column');
        $header->setLevel(1);
        $header->setSort(0);
        $header->setOwner($template);
        $header->setParent($template);
        $this->em->persist($header);

        $main = new LayoutBlock;
        $main->setType('one_column');
        $main->setLevel(1);
        $main->setSort(1);
        $main->setOwner($template);
        $main->setParent($template);
        $this->em->persist($header);

        $paragraph = new ContentBlock;
        $paragraph->setLevel(2);
        $paragraph->setSort(0);
        $paragraph->setOwner($template);
        $paragraph->setParent($main);
        $paragraph->setContent('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>');
        $this->em->persist($paragraph);

        $footer = new LayoutBlock;
        $footer->setType('one_column');
        $footer->setLevel(1);
        $footer->setSort(2);
        $footer->setOwner($template);
        $footer->setParent($template);
        $this->em->persist($footer);

        $this->em->flush();

    }

}