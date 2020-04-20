<?php

namespace Opifer\EavBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Opifer\EavBundle\Model\Attribute;
use Opifer\EavBundle\Model\Schema;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testName()
    {
        $attribute = new Attribute();
        $name = 'Some Name';

        $expected = $name;
        $attribute->setName($name);
        $actual = $attribute->getName();

        $this->assertSame($expected, $actual);
    }

    public function testDisplayName()
    {
        $attribute = new Attribute();
        $displayName = 'Some Displayname';

        $expected = $displayName;
        $attribute->setDisplayName($displayName);
        $actual = $attribute->getDisplayName();

        $this->assertSame($expected, $actual);
    }

    public function testDescription()
    {
        $attribute = new Attribute();
        $description = 'Some Description';

        $expected = $description;
        $attribute->setDescription($description);
        $actual = $attribute->getDescription();

        $this->assertSame($expected, $actual);
    }

    public function testSchema()
    {
        $attribute = new Attribute();
        $schema = new Schema();

        $expected = $schema;
        $attribute->setSchema($schema);
        $actual = $attribute->getSchema();

        $this->assertSame($expected, $actual);
    }

    public function testSort()
    {
        $attribute = new Attribute();
        $sort = 1;

        $expected = $sort;
        $attribute->setSort($sort);
        $actual = $attribute->getSort();

        $this->assertSame($expected, $actual);
    }

    public function testValueType()
    {
        $attribute = new Attribute();
        $valueType = 'Some Value Type';

        $expected = $valueType;
        $attribute->setValueType($valueType);
        $actual = $attribute->getValueType();

        $this->assertSame($expected, $actual);
    }

    public function testAllowedSchemas()
    {
        $attribute = new Attribute();
        $arrayCollection = new ArrayCollection();
        $allowedSchemas = $arrayCollection;

        $expected = $arrayCollection;
        $attribute->setAllowedSchemas($allowedSchemas);
        $actual = $attribute->getAllowedSchemas();

        $this->assertSame($expected, $actual);
    }
}
