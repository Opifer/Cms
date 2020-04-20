<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\IntegerValueProvider;
use PHPUnit\Framework\TestCase;

class IntegerValueProviderTest extends TestCase
{
    public function testEntityExists()
    {
        $provider = new IntegerValueProvider();
        $this->assertTrue(class_exists($provider->getEntity()));
    }
}
