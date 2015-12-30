<?php

namespace Opifer\CmsBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Exception Router
 *
 * This router should be used at the very bottom of the chain, so it can render
 * a 404 page as soon as non of the earlier routes could've been found.
 */
class ExceptionRouter extends BaseRouter implements RouterInterface
{
    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        $this->routeCollection->add('_404', new Route('/{anything}', [
            '_controller' => 'OpiferCmsBundle:Frontend/Exception:error404',
            '_locale'     => $this->container->getParameter('locale'),
            'anything'    => '',
        ], [
            'anything'        => "[a-zA-Z0-9\-_\/]*",
        ]));
    }

    /**
     * Matches anything.
     *
     * @param string $pathinfo The path info to be parsed (raw format, i.e. not
     *                         urldecoded)
     *
     * @return array An array of parameters
     */
    public function match($pathinfo)
    {
        $urlMatcher = new UrlMatcher($this->routeCollection, $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        return $result;
    }
}
