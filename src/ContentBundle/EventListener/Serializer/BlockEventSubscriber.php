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
        $block = $event->getObject();

        if (!$block instanceof Block) {
            return;
        }

        $service = $this->blockManager->getService($block);
        $service->load($block);

        $properties = $block->getProperties();

        if (isset($properties['displayLogic']) && !empty($properties['displayLogic'])) {
            $properties['displayLogic'] = json_decode($properties['displayLogic'], true);
            $block->setProperties($properties);
        }
    }
}
