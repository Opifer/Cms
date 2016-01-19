<?php

namespace Opifer\MediaBundle\Model;

interface MediaManagerInterface
{
    /**
     * Create media.
     *
     * @return MediaInterface
     */
    public function createMedia();

    /**
     * Save a media item.
     *
     * @param MediaInterface $media
     */
    public function save(MediaInterface $media);

    /**
     * Remove a media item.
     *
     * @param MediaInterface $media
     */
    public function remove(MediaInterface $media);

    /**
     * Get the media class.
     *
     * @return string
     */
    public function getClass();

    /**
     * Get the media class.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository();
}
