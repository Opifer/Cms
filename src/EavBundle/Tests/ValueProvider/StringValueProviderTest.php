<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\StringValueProvider;
use PHPUnit\Framework\TestCase;

class StringValueProviderTest extends TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new StringValueProvider();
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
