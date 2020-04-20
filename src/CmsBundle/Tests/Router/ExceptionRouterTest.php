<?php

namespace Opifer\CmsBundle\Tests\Router;

use Mockery as m;
use Monolog\Test\TestCase;
use Opifer\CmsBundle\Router\ExceptionRouter;
use Symfony\Component\HttpFoundation\Request;

class ExceptionRouterTest extends TestCase
{
    public function testMatch(): void
    {
        $expected = [
            '_controller' => 'OpiferCmsBundle:Frontend/Exception:error404',
            '_locale' => 'en',
            'anything' => 'some/route',
            '_route' => '_404',
        ];
        $request = new Request();

        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->shouldReceive('get')->with('request')->andReturn($request);
        $container->shouldReceive('getParameter')->andReturn('en');

        $router = new ExceptionRouter($container);
        $actual = $router->match('/some/route');

        $this->assertEquals($expected, $actual);
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
