<?php

namespace Opifer\CmsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Listens to the security.interactive_login event, to determine where the
 * user should be redirected to
 */
class RedirectAfterLoginListener
{
    protected $router;
    protected $security;
    protected $dispatcher;
    protected $requestStack;

    public function __construct(RouterInterface $router, SecurityContext $security, EventDispatcherInterface $dispatcher)
    {
        $this->router = $router;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
    }

    /**
     * The method called after a successful login
     *
     * @param InteractiveLoginEvent $event
     *
     * @return void
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }

    /**
     * Check user roles and redirect accordingly
     *
     * Note about HTTP_REFERER:
     * The address of the page (if any) which referred the user agent to the current page.
     * This is set by the user agent. Not all user agents will set this, and some provide
     * the ability to modify HTTP_REFERER as a feature. In short, it cannot really be trusted.
     *
     * @param FilterResponseEvent $event
     *
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $referer = $event->getRequest()->headers->get('referer');

        // Overwrite the url with our own. Don't set a new RedirectResponse because we will lose other
        // attributes like cookies.
        if ($event->getResponse() instanceof RedirectResponse) {

            if ($this->security->isGranted('ROLE_ADMIN') && strpos($referer, 'admin') !== false) {
                $newRoute = $this->router->generate('opifer.cms.dashboard.view');
            } else {
                $newRoute = $this->router->generate('opifer_account');
            }

            if ($newRoute) {
                $event->getResponse()->setTargetUrl($newRoute);
            }
        }
    }
}
