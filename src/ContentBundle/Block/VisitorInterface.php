<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Interface VisitorInterface
 *
 * @package Opifer\ContentBundle\Block
 */
interface VisitorInterface
{
    /**
     * @param BlockInterface $block
     */
    public function visit(BlockInterface $block);
}