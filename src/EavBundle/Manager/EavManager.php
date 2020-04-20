<?php

namespace Opifer\EavBundle\Manager;

use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\SchemaInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\ValueProvider\Pool;

class EavManager
{
    /** @var \Opifer\EavBundle\ValueProvider\Pool */
    protected $providerPool;

    /** @var string */
    protected $valueSetClass;

    /**
     * Constructor
     *
     * @param Pool   $providerPool
     * @param string $valueSetClass
     */
    public function __construct(Pool $providerPool, $valueSetClass)
    {
        $this->providerPool = $providerPool;
        $this->valueSetClass = $valueSetClass;
    }

    /**
     * Initializes an entity from a schema to work properly with this bundle.
     *
     * @param SchemaInterface $schema
     *
     * @throws \Exception If the entity is not an instance of EntityInterface
     *
     * @return EntityInterface
     */
    public function initializeEntity(SchemaInterface $schema)
    {
        $valueSet = $this->createValueSet();
        $valueSet->setSchema($schema);

        // To avoid persisting Value entities with no actual value to the database
        // we create empty ones, that will be removed on postPersist events.
        $this->replaceEmptyValues($valueSet);

        $entity = $schema->getObjectClass();
        $entity = new $entity();

        if (!$entity instanceof EntityInterface) {
            throw new \Exception(sprintf('The entity specified in schema "%d" schema must implement Opifer\EavBundle\Model\EntityInterface.', $schema->getId()));
        }

        $entity->setValueSet($valueSet);
        $entity->setSchema($schema);

        return $entity;
    }

    /**
     * Creates empty entities for non-persisted attributes.
     *
     * @param ValueSetInterface $valueSet
     *
     * @return array
     */
    public function replaceEmptyValues(ValueSetInterface $valueSet)
    {
        // collect persisted attributevalues
        $persistedAttributes = [];
        foreach ($valueSet->getValues() as $value) {
            $persistedAttributes[] = $value->getAttribute();
        }

        $newValues = [];

        // Create empty entities for missing attributes
        $missingAttributes = array_diff($valueSet->getAttributes()->toArray(), $persistedAttributes);
        foreach ($missingAttributes as $attribute) {
            $provider = $this->providerPool->getValue($attribute->getValueType());
            $valueClass = $provider->getEntity();

            $value = new $valueClass();
            $valueSet->addValue($value);
            $value->setValueSet($valueSet);
            $value->setAttribute($attribute);

            $newValues[] = $value;
        }

        return $newValues;
    }

    /**
     * @return ValueSetInterface
     */
    public function createValueSet()
    {
        $valueSetClass = $this->valueSetClass;

        return new $valueSetClass();
    }
}
