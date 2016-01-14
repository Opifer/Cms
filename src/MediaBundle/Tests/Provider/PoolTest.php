<?php

namespace Opifer\MediaBundle\Tests\Provider;

use \Mockery as m;
use Opifer\MediaBundle\Provider\Pool;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    public function testProvidersIsArray()
    {
        $pool = new Pool();
        $this->assertInternalType('array', $pool->getProviders());
    }

    public function testAddProvider()
    {
        $provider = m::mock('Opifer\MediaBundle\Provider\ProviderInterface');

        $pool = new Pool();
        $pool->addProvider($provider, 'provider');

        $providers = $pool->getProviders();

        $this->assertArrayHasKey('provider', $providers);
        $this->assertInstanceOf('Opifer\MediaBundle\Provider\ProviderInterface', $providers['provider']);
    }

    public function tearDown()
    {
        m::close();
    }
}
