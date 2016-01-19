<?php

namespace Opifer\EavBundle\Tests\TestData;

use Opifer\EavBundle\Entity\NestedValue;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\Nestable;
use Opifer\EavBundle\Model\SchemaInterface;
use Opifer\EavBundle\Model\ValueSetInterface;

class Entity implements EntityInterface
{
    protected $valueSet;

    protected $schema;

    protected $nestedSort;

    public function setValueSet(ValueSetInterface $valueSet)
    {
        $this->valueSet = $valueSet;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }

    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
