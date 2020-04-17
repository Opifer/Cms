<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\IntegerValueProvider;
use PHPUnit\Framework\TestCase;

class IntegerValueProviderTest extends TestCase
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
