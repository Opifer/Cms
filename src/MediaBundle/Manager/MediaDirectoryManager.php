<?php

namespace Opifer\MediaBundle\Manager;

use Doctrine\ORM\EntityManager;

class MediaDirectoryManager
{
    protected $class;

    protected $em;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function create()
    {
        $class = $this->getClass();

        return new $class();
    }

    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
