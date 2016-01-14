<?php

namespace Opifer\MediaBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Opifer\MediaBundle\Model\MediaInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Media Entity Listener
 *
 * This class listens to Doctrine events and calls the matching method on
 * the Media provider.
 *
 * Documentation:
 * http://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html
 *
 * Because we cannot define the Doctrine's EntityListeners as a service yet,
 * we do an instanceof check on every method. Later, we should switch to:
 * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#entity-listeners
 */
class MediaListener implements EventSubscriber
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor
     *
     * Requires the complete container, to avoid circular references.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::preRemove,
            Events::postRemove
        ];
    }

    /**
     * Get the provider pool
     *
     * @return \Opifer\MediaBundle\Provider\ProviderInterface
     */
    public function getProvider(LifecycleEventArgs $args)
    {
        $provider = $args->getObject()->getProvider();

        if (!$provider) {
            throw new \Exception('Please set a provider on the entity before persisting any media');
        }

        return $this->container->get('opifer.media.provider.pool')->getProvider($provider);
    }

    /**
     * Post Load handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->postLoad($args->getObject());
        }
    }

    /**
     * Pre persist handler
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->prePersist($args->getObject());
        }
    }

    /**
     * Post persist handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->postPersist($args->getObject());
        }
    }

    /**
     * Pre update handler
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->preUpdate($args->getObject());
        }
    }

    /**
     * Post update handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->postUpdate($args->getObject());
        }
    }

    /**
     * Pre remove handler
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->preRemove($args->getObject());
        }
    }

    /**
     * Post remove handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof MediaInterface) {
            $this->getProvider($args)->postRemove($args->getObject());
        }
    }
}
