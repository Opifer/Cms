<?php

namespace Opifer\EavBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\Attribute;
use Opifer\EavBundle\Model\Option;
use Opifer\EavBundle\Model\ValueSet;

class ValueTest extends \PHPUnit_Framework_TestCase
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

    public function testOption()
    {
//        $valueEntity = new Value();
//        $arrayCollection = new ArrayCollection();
//        $option = new Option();
//        $arrayCollection->add($option);
//
//        $expected = $arrayCollection;
//        $valueEntity->setOptions($arrayCollection);
//        $actual = $valueEntity->getOptions();
//
//        $this->assertTrue($actual->count() == 1);
//        $this->assertEquals($option, $actual->get(0));
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