<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\IntegerValueProvider;

class IntegerValueProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new IntegerValueProvider();
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
