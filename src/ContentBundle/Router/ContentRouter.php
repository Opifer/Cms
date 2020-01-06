<?php

namespace Opifer\ContentBundle\Router;

use Doctrine\ORM\NoResultException;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Content Router.
 */
class ContentRouter implements RouterInterface
{
    /** @var RequestContext */
    protected $context;

    /** @var RouteCollection */
    protected $routeCollection;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var ContentManagerInterface */
    protected $contentManager;

    /** @var RequestStack */
    protected $requestStack;

    /** @var string */
    protected $frontendUrl;

    /**
     * Constructor
     *
     * @param RequestStack            $requestStack
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(RequestStack $requestStack, ContentManagerInterface $contentManager, $frontendUrl)
    {
        $this->routeCollection = new RouteCollection();
        $this->requestStack = $requestStack;
        $this->contentManager = $contentManager;
        $this->frontendUrl = $frontendUrl;

        $this->createRoutes();
    }

    /**
     * Create the routes.
     */
    private function createRoutes()
    {
        if ($this->frontendUrl) {
            $this->routeCollection->add('_content', new Route('/{slug}', [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => $this->frontendUrl,
            ], [
                'slug'        => "[a-zA-Z0-9\-_\/]*",
            ], [
                'expose'      => true,
                'utf8'        => true,
            ]));
        } else {
            $this->routeCollection->add('_content', new Route('/{slug}', [
                '_controller' => 'OpiferContentBundle:Frontend/Content:view',
                'slug'        => '',
            ], [
                'slug'        => "[a-zA-Z0-9\-_\/]*",
            ], [
                'expose'      => true,
                'utf8'        => true,
            ]));
        }
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
     */
    public function match($pathinfo)
    {
        $urlMatcher = new UrlMatcher($this->routeCollection, $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        if (!$this->frontendUrl && !empty($result)) {
            // The route matches, now check if it actually exists
            $slug = $result['slug'];
            $host = $this->getRequest()->getHost();

            $contentRepository = $this->contentManager->getRepository();

            try {
                //is it directory index
                if (substr($slug, -1) == '/') {
                    $slug = rtrim($slug, '/');
                }

                $result['content'] = $contentRepository->findActiveBySlug($slug, $host);
            } catch (NoResultException $e) {
                try {
                    $result['content'] = $contentRepository->findActiveByAlias($slug, $host);
                } catch (NoResultException $ex) {
                    throw new ResourceNotFoundException('No page found for slug '.$pathinfo);
                }
            }
        }

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
            $this->context = new RequestContext();
            $this->context->fromRequest($this->getRequest());
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
        return $this->routeCollection;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
