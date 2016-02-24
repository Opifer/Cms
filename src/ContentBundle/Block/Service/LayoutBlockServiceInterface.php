<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface LayoutBlockServiceInterface
 *
 * @Widget;
 *
 * @package Opifer\ContentBundle\Block
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
