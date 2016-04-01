<?php

namespace Opifer\ContentBundle\Model;

interface BlockInterface
{
    /**
     * The string returned should match Block's service name
     *
     * @return string
     */
    public function getBlockType();


    public function isInRoot();

    public function getOwner();

    public function setDraft($draft);

    /**
     * Should return an array of block properties
     *
     * @return array
     */
    public function getProperties();

    /**
     * Returns the content
     *
     * @return ContentInterface
     */
    public function getContent();
}
