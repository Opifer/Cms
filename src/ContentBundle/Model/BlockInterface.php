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

    public function setDraft($draft);
}