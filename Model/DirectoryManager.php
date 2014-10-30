<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class DirectoryManager
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $class;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $em
     * @param string                 $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Create a new directory instance
     *
     * @return DirectoryInterface
     */
    public function create()
    {
        $class = $this->getClass();
        $directory = new $class();

        return $directory;
    }

    /**
     * Save directory
     *
     * @param  DirectoryInterface $directory
     *
     * @return DirectoryInterface
     */
    public function save(DirectoryInterface $directory)
    {
        $this->em->persist($directory);
        $this->em->flush();

        return $directory;
    }

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
