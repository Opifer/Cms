<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\Tests\TestData\TestValueProvider;

class AbstractValueProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNameFromClassName()
    {
        $provider = new TestValueProvider();

        $this->assertEquals('test', $provider->getName());
    }

    public function testGetCapitalizedLabelFromClassName()
    {
        $provider = new TestValueProvider();

        $this->assertEquals('Test', $provider->getLabel());
    }
}
