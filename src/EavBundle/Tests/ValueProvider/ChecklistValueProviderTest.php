<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\Tests\TestData\Option;
use Opifer\EavBundle\ValueProvider\ChecklistValueProvider;
use PHPUnit\Framework\TestCase;

class ChecklistValueProviderTest extends TestCase
{
    public function testEntityExists()
    {
        $provider = new ChecklistValueProvider(Option::class);
        $this->assertTrue(class_exists($provider->getEntity()));
    }
}
