<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class DirectoryManager implements DirectoryManagerInterface
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
        if (!is_subclass_of($class, 'Opifer\ContentBundle\Model\DirectoryInterface')) {
            throw new \Exception($class .' must implement Opifer\ContentBundle\Model\DirectoryInterface');
        }

        $this->em = $em;
        $this->class = $class;
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
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria = [])
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findChildren($parent = null)
    {
        if ((int) $parent) {
            $curDirectory = $this->find($parent);
            $directories = $curDirectory->getChildren();
        } else {
            $directories = $this->findBy(['parent' => null]);
        }

        return $directories;
    }

    /**
     * {@inheritDoc}
     */
    public function getTree()
    {
        $repository = $this->getRepository();

        if (true !== $errors = $repository->verify()) {
            throw new \Exception('Directory tree is invalid');
        }

        $repository->recover();

        $this->em->flush();

        return $repository->childrenHierarchy();
    }

    /**
     * {@inheritDoc}
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
     * Remove a directory
     *
     * @param  DirectoryInterface $directory
     */
    public function remove(DirectoryInterface $directory)
    {
        $this->em->remove($directory);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
