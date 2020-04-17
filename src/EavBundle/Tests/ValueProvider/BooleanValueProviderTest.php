<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\BooleanValueProvider;
use PHPUnit\Framework\TestCase;

class BooleanValueProviderTest extends TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new BooleanValueProvider(\Opifer\EavBundle\Tests\TestData\Option::class);
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
