<?php

namespace Opifer\RedirectBundle\Router;

use Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Opifer\RedirectBundle\Model\RedirectManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirect Router.
 */
class RedirectRouter implements RouterInterface
{
    /** @var RequestContext */
    protected $context;

    /** @var RouteCollection */
    protected $routeCollection;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var RedirectManagerInterface */
    protected $redirectManager;

    /** @var Request */
    protected $request;
    
    /** @var array */
    protected $redirects;

    /**
     * The constructor for this service.
     *
     * @param ContainerInterface $container
     */
    public function __construct(RequestStack $requestStack, RedirectManagerInterface $redirectManager)
    {
        $this->routeCollection = new RouteCollection();
        $this->request = $requestStack->getCurrentRequest();
        $this->redirectManager = $redirectManager;
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
        $urlMatcher = new RedirectableUrlMatcher($this->getRouteCollection(), $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        return $result;
    }

    /**
     * Generate an url for a supplied route.
     *
     * @param string $name       The path
     * @param array  $parameters The route parameters
     * @param bool   $absolute   Absolute url or not
     *
     * @return null|string
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        throw new RouteNotFoundException("You cannot generate a url from a redirect");
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
            $this->context = new RequestContext();
            $this->context->fromRequest($this->request);
        }

        return $this->context;
    }

    /**
     * Getter for routeCollection.
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        if ($this->redirects === null) {
            $this->redirects = $this->redirectManager->getRepository()->findAll();
            
            foreach ($this->redirects as $redirect) {
                $this->routeCollection->add('_redirect_'.$redirect->getId(), new Route($redirect->getOrigin(), [
                    '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                    'path' => $redirect->getTarget(),
                    'permanent' => $redirect->isPermanent(),
                ]));
            }
        }
        
        return $this->routeCollection;
    }
}
