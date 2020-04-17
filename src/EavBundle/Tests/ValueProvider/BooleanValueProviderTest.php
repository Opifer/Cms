<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\Tests\TestData\Option;
use Opifer\EavBundle\ValueProvider\BooleanValueProvider;
use PHPUnit\Framework\TestCase;

class BooleanValueProviderTest extends TestCase
{
    public function testEntityExists()
    {
        $provider = new BooleanValueProvider(Option::class);
        $this->assertTrue(class_exists($provider->getEntity()));
    }
}
