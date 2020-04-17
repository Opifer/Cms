<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\TextValueProvider;
use PHPUnit\Framework\TestCase;

class TextValueProviderTest extends TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new TextValueProvider();
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
