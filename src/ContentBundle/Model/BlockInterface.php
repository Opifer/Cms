<?php

namespace Opifer\ContentBundle\Model;

/**
 * Class BlockInterface
 *
 * @package Opifer\ContentBundle\Model
 */
interface BlockInterface
{
    /**
     * The string returned should match Block's service name
     *
     * @return string
     */
    public function getBlockType();

    /**
     * Version number (grouped) of the entire tree.
     *
     * @return integer
     */
    public function getRootVersion();
}