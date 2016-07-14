<?php

namespace Opifer\EavBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class OptionManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var string */
    protected $class;

    /**
     * Constructor
     *
     * @throws \Exception If the passed class is no sub class of OptionInterface
     *
     * @param EntityManagerInterface $em
     * @param string                 $class
     */
    public function __construct(EntityManagerInterface $em, $class)
    {
        if (!is_subclass_of($class, 'Opifer\EavBundle\Model\OptionInterface')) {
            throw new \Exception($class.' must implement Opifer\EavBundle\Model\OptionInterface');
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
     * Create a new option instance
     *
     * @return OptionInterface
     */
    public function create()
    {
        $class = $this->getClass();
        $option = new $class();

        return $option;
    }

    /**
     * Save option
     *
     * @param OptionInterface $option
     *
     * @return OptionInterface
     */
    public function save(OptionInterface $option)
    {
        $this->em->persist($option);
        $this->em->flush();

        return $option;
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
