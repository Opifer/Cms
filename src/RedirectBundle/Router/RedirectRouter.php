<?php

namespace Opifer\RedirectBundle\Router;

use Opifer\RedirectBundle\Model\Redirect;
use Opifer\RedirectBundle\Model\RedirectManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Redirect Router.
 */
class RedirectRouter implements RouterInterface
{
    /** @var RequestContext */
    protected $context;

    /** @var RouteCollection */
    protected $routeCollection;

    /** @var RedirectManagerInterface */
    protected $redirectManager;

    /** @var Request */
    protected $request;
    
    /** @var array */
    protected $redirects;

    /**
     * The constructor for this service.
     *
     * @param RequestStack $requestStack
     * @param RedirectManagerInterface $redirectManager
     */
    public function __construct(RequestStack $requestStack, RedirectManagerInterface $redirectManager)
    {
        $this->routeCollection = new RouteCollection();
        $this->request = $requestStack->getCurrentRequest();
        $this->redirectManager = $redirectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $urlMatcher = new UrlMatcher($this->getRouteCollection(), $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        $path = $result['path'];

        preg_match_all('/{(.*?)}/', $path, $matches);
        if (!empty($matches) && isset($matches[1])) {
            foreach ($matches[1] as $match) {
                if (array_key_exists($match, $result)) {
                    $path = str_replace('{'.$match .'}', $result[$match], $path);
                }
            }
        }

        $result['path'] = $path;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        throw new RouteNotFoundException("You cannot generate a url from a redirect");
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if ($this->redirects === null) {
            $this->redirects = $this->redirectManager->getRepository()->findAll();

            /** @var Redirect $redirect */
            foreach ($this->redirects as $redirect) {
                $this->routeCollection->add('_redirect_'.$redirect->getId(), new Route($redirect->getOrigin(), [
                    '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                    'path' => $redirect->getTarget(),
                    'permanent' => $redirect->isPermanent(),
                ], $this->redirectManager->formatRouteRequirements($redirect)));
            }
        }
        
        return $this->routeCollection;
    }
}
