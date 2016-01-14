<?php

namespace Opifer\EavBundle\Model;

use Doctrine\ORM\EntityManagerInterface;

class SchemaManager
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
        if ( ! is_subclass_of($class, 'Opifer\EavBundle\Model\SchemaInterface')) {
            throw new \Exception($class . ' must implement Opifer\EavBundle\Model\SchemaInterface');
        }

        $this->em    = $em;
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
     * Create a new schema instance
     *
     * @return SchemaInterface
     */
    public function create()
    {
        $class    = $this->getClass();
        $schema = new $class();

        return $schema;
    }


    /**
     * Save schema
     *
     * @param SchemaInterface $schema
     *
     * @return SchemaInterface
     */
    public function save(SchemaInterface $schema)
    {
        foreach ($schema->getAttributes() as $attribute) {
            $attribute->setSchema($schema);
            $this->em->persist($attribute);
        }
        $this->em->persist($schema);
        $this->em->flush();

        return $schema;
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
