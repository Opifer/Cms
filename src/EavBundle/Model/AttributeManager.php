<?php

namespace Opifer\EavBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class AttributeManager
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
        if (!is_subclass_of($class, 'Opifer\EavBundle\Model\AttributeInterface')) {
            throw new \Exception($class.' must implement Opifer\EavBundle\Model\AttributeInterface');
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
     * Create a new attribute instance
     *
     * @return AttributeInterface
     */
    public function create()
    {
        $class = $this->getClass();
        $attribute = new $class();

        return $attribute;
    }

    /**
     * Save attribute
     *
     * @param AttributeInterface $attribute
     *
     * @return AttributeInterface
     */
    public function save(AttributeInterface $attribute)
    {
        $this->em->persist($attribute);
        $this->em->flush();

        return $attribute;
    }

    /**
     * Get repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }
}
