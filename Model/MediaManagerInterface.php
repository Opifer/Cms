<?php

namespace Opifer\MediaBundle\Model;

interface MediaManagerInterface
{
    /**
     * Create media
     *
     * @return MediaInterface
     */
    public function createMedia();

    /**
     * Get the media class
     *
     * @return string
     */
    public function getClass();

    /**
     * Get the media class
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository();
}
