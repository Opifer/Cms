<?php

namespace Opifer\ContentBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Environment\Environment;

class BlockEventSubscriber implements EventSubscriberInterface
{
    protected $blockManager;

    protected $environment;

    /**
     * BlockEventSubscriber constructor.
     *
     * @param BlockManager $blockManager
     * @param Environment  $environment
     */
    public function __construct(BlockManager $blockManager, Environment $environment)
    {
        $this->blockManager = $blockManager;
        $this->environment = $environment;
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

        // Set children to avoid unnecessary duplicate queries
        $this->setChildren($block);

        $this->decodeDisplayLogic($block);
    }

    /**
     * Maps the block children from the environment to the block
     *
     * @param Block $block
     */
    protected function setChildren(Block $block)
    {
        if (method_exists($block, 'setChildren')) {
            $children = $this->environment->getBlockChildren($block);
            $block->setChildren($children);
        }
    }

    protected function decodeDisplayLogic(Block $block)
    {
        $properties = $block->getProperties();

        if (isset($properties['displayLogic']) && !empty($properties['displayLogic'])) {
            $properties['displayLogic'] = json_decode($properties['displayLogic'], true);
            $block->setProperties($properties);
        }
    }
}
