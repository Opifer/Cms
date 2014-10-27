<?php

namespace Opifer\EavBundle\Tests\ValueProvider;

use Opifer\EavBundle\ValueProvider\HtmlValueProvider;

class HtmlValueProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;

    public function __construct()
    {
        $this->provider = new HtmlValueProvider();
    }

    public function testEntityExists()
    {
        $this->assertTrue(class_exists($this->provider->getEntity()));
    }
}
