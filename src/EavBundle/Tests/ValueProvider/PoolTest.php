<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\Tests\TestData\TestValueProvider;
use Opifer\EavBundle\ValueProvider\Pool;
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    private $pool;

    public function setUp(): void
    {
        $this->pool = new Pool();

        $provider = new TestValueProvider();
        $this->pool->addValue($provider, 'test');
    }

    public function testAddValue()
    {
        $provider = new TestValueProvider();

        $this->pool->addValue($provider, 'secondtest');

        $this->assertArrayHasKey('secondtest', $this->pool->getValues());

        $actual = $this->pool->getValue('secondtest');

        $this->assertInstanceOf(TestValueProvider::class, $actual);
    }

    public function testGetValueByEntity()
    {
        $actual = $this->pool->getValueByEntity('My\Entity\TestValue');

        $this->assertInstanceOf(TestValueProvider::class, $actual);
    }
}
