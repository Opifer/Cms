<?php

namespace Opifer\MediaBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Opifer\MediaBundle\Model\Media;

/**
 * Class MediaEventSubscriber
 *
 * @package  Opifer\MediaBundle\EventListener\Serializer
 */
class MediaEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var FilterConfiguration
     */
    private $filterConfig;

    /**
     * Constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager, FilterConfiguration $filterConfig)
    {
        $this->cacheManager = $cacheManager;
        $this->filterConfig = $filterConfig;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'),
        );
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        // getSubscribedEvents doesn't seem to support parent classes
        if (!$event->getObject() instanceof Media) {
            return;
        }

        if ($event->getObject()->getProvider() == 'image') {
            $reference = $event->getObject()->getReference();
        } else {
            $reference = $event->getObject()->getThumb()->getReference();
        }

        $filters = array_keys($this->filterConfig->all());

        $variants = [];
        foreach ($filters as $filter) {
            $variants[$filter] = $this->cacheManager->getBrowserPath($reference, $filter);
        }

        $event->getVisitor()->addData('images', $variants);
    }
}
