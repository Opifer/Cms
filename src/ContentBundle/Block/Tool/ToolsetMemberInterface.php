<?php

namespace Opifer\ContentBundle\Block\Tool;

use Opifer\ContentBundle\Model\BlockInterface;

interface ToolsetMemberInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return Tool
     */
    public function getTool(BlockInterface $block = null);
}
