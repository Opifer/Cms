<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Attribute;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    public function testParameters()
    {
        $attributes = new Attribute();
        $parameters = array();

        $expected = $parameters;
        $attributes->setParameters($parameters);
        $actual = $attributes->getParameters();

        $this->assertSame($expected, $actual);
    }
}
