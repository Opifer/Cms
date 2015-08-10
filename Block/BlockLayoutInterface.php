<?php

namespace Opifer\ContentBundle\Block;

/**
 * Interface BlockLayoutInterface
 *
 * @Widget;
 *
 * @package Opifer\ContentBundle\Block
 */
interface BlockLayoutInterface
{

    /**
     * @return array
     */
    public function getPlaceholders();
}