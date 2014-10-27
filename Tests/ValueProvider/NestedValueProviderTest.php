<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\NestedValueProvider;

class NestedValueProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new NestedValueProvider();
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
