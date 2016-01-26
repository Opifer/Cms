<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\EavBundle\Model\SchemaManager;

class ContentTypeManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var SchemaManager */
    protected $schemaManager;

    /** @var string */
    protected $class;

    /** @var ContentManager */
    protected $contentManager;

    /**
     * @param EntityManagerInterface $em
     * @param SchemaManager          $schemaManager
     * @param ContentManager         $contentManager
     * @param string                 $class
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $em, SchemaManager $schemaManager, ContentManager $contentManager, $class)
    {
        if (!is_subclass_of($class, 'Opifer\ContentBundle\Model\ContentTypeInterface')) {
            throw new \Exception(sprintf('%s must implement Opifer\ContentBundle\Model\ContentTypeInterface', $class));
        }

        $this->em = $em;
        $this->schemaManager = $schemaManager;
        $this->contentManager = $contentManager;
        $this->class = $class;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Create a new contentType instance.
     *
     * @return ContentType
     */
    public function create()
    {
        $class = $this->getClass();
        $contentType = new $class();

        $schema = $this->schemaManager->create();
        $schema->setObjectClass($this->contentManager->getClass());

        $contentType->setSchema($schema);

        return $contentType;
    }

    /**
     * Save attribute.
     *
     * @param ContentType $contentType
     *
     * @return ContentType
     */
    public function save(ContentType $contentType)
    {
        $this->em->persist($contentType);
        $this->em->flush();

        return $contentType;
    }

    /**
     * Get repository.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getClass());
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->getRepository()->findAll();
    }
}
