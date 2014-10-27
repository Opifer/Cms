<?php

namespace Opifer\EavBundle\Tests\TestData;

use Opifer\EavBundle\Eav\EntityInterface;
use Opifer\EavBundle\Entity\Template;
use Opifer\EavBundle\Entity\ValueSet;

class Entity implements EntityInterface
{
    protected $valueSet;

    protected $template;

    public function setValueSet(ValueSet $valueSet)
    {
        $this->valueSet = $valueSet;
    }

    public function getValueSet()
    {
        return $this->valueSet;
    }

    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }
}
