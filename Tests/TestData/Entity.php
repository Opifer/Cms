<?php

namespace Opifer\EavBundle\Tests\TestData;

use Opifer\EavBundle\Entity\NestedValue;
use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\Nestable;
use Opifer\EavBundle\Model\TemplateInterface;
use Opifer\EavBundle\Model\ValueSetInterface;

class Entity implements EntityInterface, Nestable
{
    protected $valueSet;

    protected $template;

    protected $nestedIn;

    protected $nestedSort;

    public function setValueSet(ValueSetInterface $valueSet)
    {
        $this->valueSet = $valueSet;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }

    public function setTemplate(TemplateInterface $template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setNestedIn(NestedValue $value)
    {
        $this->nestedIn = $value;

        return $this;
    }

    public function getNestedIn()
    {
        return $this->nestedIn;
    }

    public function setNestedSort($sort)
    {
        $this->nestedSort = $sort;

        return $this;
    }

    public function getNestedSort()
    {
        return $this->nestedSort;
    }
}
