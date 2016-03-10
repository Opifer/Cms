<?php

namespace Opifer\CmsBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class DoctrineExtensionListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onLateKernelRequest(GetResponseEvent $event)
    {
        $translatable = $this->container->get('gedmo.listener.translatable');
        $translatable->setTranslatableLocale($event->getRequest()->getLocale());
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $tokenStorage = $this->container->get('security.token_storage', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $authorizationChecker = $this->container->get('security.authorization_checker', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (null !== $tokenStorage && null !== $authorizationChecker && null !== $tokenStorage->getToken() && $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if ($this->container->has('opifer.revisions.revision_listener')) {
                $listener = $this->container->get('opifer.revisions.revision_listener');
                $listener->setUsername($tokenStorage->getToken()->getUsername());
            }
        }
    }
}
