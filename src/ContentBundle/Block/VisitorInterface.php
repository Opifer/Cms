<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;

interface VisitorInterface
{
    /**
     * @param BlockInterface $block
     */
    public function visit(BlockInterface $block);
}
