<?php

namespace Opifer\ReviewBundle\Manager;

use Doctrine\ORM\EntityManager;
use Opifer\ReviewBundle\Model\ReviewInterface;

class ReviewManager
{
    /** @var EntityManager  */
    protected $em;

    /** @var string  */
    protected $class;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return ReviewInterface
     */
    public function createClass()
    {
        $class = $this->class;

        return new $class();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
    }
}
