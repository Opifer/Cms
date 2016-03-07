<?php

namespace Opifer\ContentBundle\Block;

/**
 * Interface BlockOwnerInterface
 *
 * @package Opifer\ContentBundle\Block
 */
interface BlockOwnerInterface
{
    /**
     * @return BlockOwnerInterface|false
     */
    public function getSuper();
}