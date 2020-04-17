<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\SelectValueProvider;
use PHPUnit\Framework\TestCase;

class SelectValueProviderTest extends TestCase
{
    private $provider;

    public function setUp(): void
    {
        $option = 'Opifer\EavBundle\Tests\TestData\Option';
        $this->provider = new SelectValueProvider($option);
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
