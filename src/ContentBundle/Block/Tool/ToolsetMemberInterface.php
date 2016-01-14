<?php

namespace Opifer\ContentBundle\Block\Tool;

/**
 * Interface ToolHolderInterface
 *
 * @package Opifer\ContentBundle\Block\Tool
 */
interface ToolsetMemberInterface
{
    /**
     * @return Tool
     */
    public function getTool();
}