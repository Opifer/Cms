<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Interface BlockContainerInterface
 *
 * @package Opifer\ContentBundle\Block
 */
interface BlockContainerInterface extends BlockInterface
{

    /**
     * @return ArrayCollection
     */
    public function getChildren();

    /**
     * @param mixed $children
     */
    public function setChildren($children);

    /**
     * Add Block
     *
     * @param BlockInterface $child
     *
     * @return BlockInterface
     */
    public function addChild(BlockInterface $child);

    /**
     * Remove Block
     *
     * @param BlockInterface $child
     *
     * @return BlockInterface
     */
    public function removeChild(BlockInterface $child);
}