<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\Tests\TestData\TestValueProvider;
use PHPUnit\Framework\TestCase;

class AbstractValueProviderTest extends TestCase
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
