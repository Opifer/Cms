<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\NestedValueProvider;

class NestedValueProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;

    public function __construct()
    {
        $nestedClass = 'Opifer\EavBundle\Tests\TestData\Entity';
        $this->provider = new NestedValueProvider($nestedClass);
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
