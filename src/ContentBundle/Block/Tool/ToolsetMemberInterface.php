<?php

namespace Opifer\ContentBundle\Block\Tool;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Interface ToolHolderInterface
 *
 * @package Opifer\ContentBundle\Block\Tool
 */
interface ToolsetMemberInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return Tool
     */
    public function getTool(BlockInterface $block = null);
}