<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class LayoutManager
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
        if (!is_subclass_of($class, 'Opifer\ContentBundle\Model\LayoutInterface')) {
            throw new \Exception($class .' must implement Opifer\ContentBundle\Model\LayoutInterface');
        }

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
     * Create a new layout instance
     *
     * @return LayoutInterface
     */
    public function create()
    {
        $class = $this->getClass();
        $layout = new $class();

        return $layout;
    }

    /**
     * Save layout
     *
     * @param  LayoutInterface $layout
     *
     * @return LayoutInterface
     */
    public function save(LayoutInterface $layout)
    {
        $this->em->persist($layout);
        $this->em->flush();

        return $layout;
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
