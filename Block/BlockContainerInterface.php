<?php

namespace Opifer\ContentBundle\Block;

/**
 * Interface BlockContainerInterface
 *
 * @package Opifer\ContentBundle\Block
 */
interface BlockContainerInterface
{
    /**
     * @return ArrayCollection
     */
    public function getChildBlocks();
}