<?php

namespace Opifer\CmsBundle\EventSubscriber;

use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DoctrineExtensionSubscriber implements EventSubscriberInterface
{
    /** @var TranslatableListener */
    private $translatableListener;

    public function __construct(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::FINISH_REQUEST => 'onLateKernelRequest'
        ];
    }

    public function onLateKernelRequest(FinishRequestEvent $event)
    {
        $this->translatableListener->setTranslatableLocale($event->getRequest()->getLocale());
    }
}
