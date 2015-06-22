<?php

namespace Opifer\CmsBundle\Tests\Router;

use Mockery as m;
use Opifer\CmsBundle\Router\ExceptionRouter;
use Symfony\Component\HttpFoundation\Request;

class ExceptionRouterTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $expected = [
            '_controller' => 'OpiferCmsBundle:Front/Exception:error404',
            '_locale' => 'en',
            'anything' => 'some/route',
            '_route' => '_404'
        ];
        $request = new Request();

        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->shouldReceive('get')->with('request')->andReturn($request);
        $container->shouldReceive('getParameter')->andReturn('en');

        $router = new ExceptionRouter($container);
        $actual = $router->match('/some/route');
        
        $this->assertEquals($expected, $actual);
    }

    public function tearDown()
    {
        m::close();
    }
}
