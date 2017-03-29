<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Interface LayoutBlockServiceInterface
 */
interface LayoutBlockServiceInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getPlaceholders(BlockInterface $block = null);
}
