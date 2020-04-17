<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Attribute;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
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
