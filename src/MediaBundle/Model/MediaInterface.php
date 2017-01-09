<?php

namespace Opifer\MediaBundle\Model;

interface MediaInterface
{
    public function getName();

    public function getReference();

    public function getProvider();

    public function getFile();

    public function setOriginal($original);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * Returns a unique cache identifier for this media object
     *
     * @return string
     */
    public function getImagesCacheKey();
}
