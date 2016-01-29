<?php

namespace Opifer\ContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Block Entity Listener.
 *
 * This class listens to Doctrine events and calls the matching method on
 * the Block Service
 */
class BlockListener implements EventSubscriber
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor.
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
            Events::postRemove,
        ];
    }

    /**
     * Get the service
     *
     * @param LifecycleEventArgs $args
     * @return \Opifer\ContentBundle\Block\BlockServiceInterface
     * @throws \Exception
     */
    public function getService(LifecycleEventArgs $args)
    {
        $service = $args->getObject();

        if (!$service) {
            throw new \Exception('Please set a provider on the entity before persisting any media');
        }

        return $this->getBlockManager()->getService($service);
    }

    /**
     * @return BlockManager
     */
    protected function getBlockManager()
    {
        return $this->container->get('opifer.content.block_manager');
    }

    /**
     * Post Load handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->postLoad($args->getObject());
        }
    }

    /**
     * Pre persist handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->prePersist($args->getObject());
        }
    }

    /**
     * Post persist handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->postPersist($args->getObject());
        }
    }

    /**
     * Pre update handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->preUpdate($args->getObject());
        }
    }

    /**
     * Post update handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->postUpdate($args->getObject());
        }
    }

    /**
     * Pre remove handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->preRemove($args->getObject());
        }
    }

    /**
     * Post remove handler.
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof BlockInterface && !$args->getObject() instanceof DocumentBlock) {
            $this->getService($args)->postRemove($args->getObject());
        }
    }
}
