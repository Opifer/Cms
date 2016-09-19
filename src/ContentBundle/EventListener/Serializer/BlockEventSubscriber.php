<?php

namespace Opifer\ContentBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Entity\Block;

class BlockEventSubscriber implements EventSubscriberInterface
{
    protected $blockManager;

    /**
     * Constructor.
     *
     * @param BlockManager $blockManager
     */
    public function __construct(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.pre_serialize', 'method' => 'preSerialize'],
        ];
    }

    /**
     * @param PreSerializeEvent $event
     */
    public function preSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();

        if (!$object instanceof Block) {
            return;
        }

        $service = $this->blockManager->getService($object);
        $service->load($object);
    }
}
