<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\Tests\TestData\TestValueProvider;
use Opifer\EavBundle\ValueProvider\Pool;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    private $pool;

    public function __construct()
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

        $expected = 'Opifer\EavBundle\Tests\TestData\TestValueProvider';
        $actual = $this->pool->getValue('secondtest');

        $this->assertInstanceOf($expected, $actual);
    }

    public function testGetValueByEntity()
    {
        $expected = 'Opifer\EavBundle\Tests\TestData\TestValueProvider';
        $actual = $this->pool->getValueByEntity('My\Entity\TestValue');

        $this->assertInstanceOf($expected, $actual);
    }
}
