<?php

namespace Opifer\EavBundle\Tests\Model;

use Mockery as m;
use Opifer\EavBundle\Model\Attribute;
use Opifer\EavBundle\Model\Option;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $option = new Option();
        $name = 'Some Name';

        $expected = $name;
        $option->setName($name);
        $actual = $option->getName();

        $this->assertSame($expected, $actual);
    }

    public function testAttribute()
    {
        $option = new Option();
        $attribute = new Attribute();

        $expected = $attribute;
        $option->setAttribute($attribute);
        $actual = $option->getAttribute();

        $this->assertSame($expected, $actual);
    }

    public function testSort()
    {
        $option = new Option();
        $sort = 1;

        $expected = $sort;
        $option->setSort($sort);
        $actual = $option->getSort();

        $this->assertSame($expected, $actual);
    }

    public function testDisplayName()
    {
        $option = new Option();
        $displayName = 'Some Displayname';

        $expected = $displayName;
        $option->setDisplayName($displayName);
        $actual = $option->getDisplayName();

        $this->assertSame($expected, $actual);
    }
}