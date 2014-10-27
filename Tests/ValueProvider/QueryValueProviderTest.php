<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\QueryValueProvider;

class QueryValueProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new QueryValueProvider();
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
