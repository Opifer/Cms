<?php

namespace Opifer\MediaBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

class MediaManager implements MediaManagerInterface
{
    protected $objectManager;
    protected $class;
    protected $repository;

    /**
     * Constructor
     *
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->class = $class;
        $this->repository = $om->getRepository($class);
    }

    /**
     * {@inheritDoc}
     */
    public function createMedia()
    {
        $class = $this->getClass();
        $media = new $class;

        return $media;
    }

    /**
     * {@inheritDoc}
     */
    public function save(MediaInterface $media)
    {
        $this->objectManager->persist($media);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(MediaInterface $media)
    {
        $this->objectManager->remove($media);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
