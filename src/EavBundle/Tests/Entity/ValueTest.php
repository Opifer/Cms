<?php

namespace Opifer\EavBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\Attribute;
use Opifer\EavBundle\Model\ValueSet;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    public function testValue()
    {
        $valueEntity = new Value();
        $value = 'Some Value';

        $expected = $value;
        $valueEntity->setValue($value);
        $actual = $valueEntity->getValue();

        $this->assertSame($expected, $actual);
    }

    public function testAttribute()
    {
        $attribute = null;
        $attributeInterface = new Attribute();
        $valueEntity = new Value();

        $expected = $attributeInterface;
        $valueEntity->setAttribute($attributeInterface);
        $actual = $valueEntity->getAttribute();

        $this->assertSame($expected, $actual);
    }

    public function testValueSet()
    {
        $valueEntity = new Value();
        $valueSet = new ValueSet();

        $expected = $valueSet;
        $valueEntity->setValueSet($valueSet);
        $actual = $valueEntity->getValueSet();

        $this->assertSame($expected, $actual);
    }

    public function testSort()
    {
        $valueEntity = new Value();
        $sort = 'Some Sort';

        $expected = $sort;
        $valueEntity->setSort($sort);
        $actual = $valueEntity->getSort();

        $this->assertSame($expected, $actual);
    }
}
