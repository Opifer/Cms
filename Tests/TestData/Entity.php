<?php

namespace Opifer\EavBundle\Tests\TestData;

use Opifer\EavBundle\Model\EntityInterface;
use Opifer\EavBundle\Model\TemplateInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\Tests\TestData\Template;
use Opifer\EavBundle\Tests\TestData\ValueSet;

class Entity implements EntityInterface
{
    protected $valueSet;

    protected $template;

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
}
