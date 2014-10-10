<?php

namespace Opifer\MediaBundle\Listener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Opifer\MediaBundle\Entity\Media;
use Opifer\MediaBundle\Provider\Pool;

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
    /** @var Pool */
    protected $pool;

    /**
     * Constructor
     *
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::preRemove,
            Events::postRemove,
        ];
    }

    /**
     * Get the provider pool
     *
     * @return Opifer\MediaBundle\Provider\ProviderInterface
     */
    public function getProvider(LifecycleEventArgs $args)
    {
        $provider = $args->getEntity()->getProvider();

        if (!$provider) {
            throw new \Exception('Please set a provider on the entity before persisting any media');
        }

        return $this->pool->getProvider($provider);
    }

    /**
     * Pre persist handler
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Media) {
            $this->getProvider($args)->prePersist($args->getEntity());
        }
    }

    /**
     * Post persist handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Media) {
            $this->getProvider($args)->postPersist($args->getEntity());
        }
    }

    /**
     * Pre update handler
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Media) {
            $this->getProvider($args)->preUpdate($args->getEntity());
        }
    }

    /**
     * Post update handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Media) {
            $this->getProvider($args)->postUpdate($args->getEntity());
        }
    }

    /**
     * Pre remove handler
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Media) {
            $this->getProvider($args)->preRemove($args->getEntity());
        }
    }

    /**
     * Post remove handler
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Media) {
            $this->getProvider($args)->postRemove($args->getEntity());
        }
    }
}
