<?php

namespace Opifer\ContentBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Content Router
 */
class ContentRouter implements RouterInterface
{
    /** @var \Symfony\Component\Routing\RequestContext */
    protected $context;

    /** @var \Symfony\Component\Routing\RouteCollection */
    protected $routeCollection;

    /** @var \Symfony\Component\Routing\Generator\UrlGenerator */
    protected $urlGenerator;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->routeCollection = new RouteCollection();

        $this->routeCollection->add('_content', new Route('/{slug}', [
            '_controller' => 'OpiferContentBundle:Frontend/Content:view',
            'slug'        => ''
        ], [
            'slug'        => "[a-zA-Z0-9\-_\/]*"
        ], [
            'expose'      => true
        ]));
    }

    /**
     * Tries to match a URL path with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the
     * exceptions documented below.
     *
     * @param string $pathinfo The path info to be parsed (raw format, i.e. not
     *                         urldecoded)
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the
     *                                   request method is not allowed
     */
    public function match($pathinfo)
    {
        $urlMatcher = new UrlMatcher($this->routeCollection, $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        if (!empty($result)) {
            // The route matches, now check if it actually exists
            $repository = $this->container->get('opifer.content.content_manager')->getRepository();
            $result['content'] = $repository->findBySlug($result['slug']);

            if (is_null($result['content']) || count($result['content']) < 1) {
                throw new ResourceNotFoundException('No page found for slug ' . $pathinfo);
            }

            $result['content'] = array_pop($result['content']);
        }

        return $result;
    }

    /**
     * Generate an url for a supplied route
     *
     * @param string $name       The path
     * @param array  $parameters The route parameters
     * @param bool   $absolute   Absolute url or not
     *
     * @return null|string
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        $this->urlGenerator = new UrlGenerator($this->routeCollection, $this->context);

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * Gets the request context.
     *
     * @return RequestContext The context
     */
    public function getContext()
    {
        if (!isset($this->context)) {
            $request = $this->container->get('request');

            $this->context = new RequestContext();
            $this->context->fromRequest($request);
        }

        return $this->context;
    }

    /**
     * Getter for routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        return $this->routeCollection;
    }
}
