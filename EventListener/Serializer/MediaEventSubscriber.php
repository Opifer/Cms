<?php

namespace Opifer\MediaBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Provider\Pool;

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
     * @var Pool
     */
    protected $pool;

    /**
     * Constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager, Pool $pool)
    {
        $this->cacheManager = $cacheManager;
        $this->pool = $pool;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'],
        ];
    }

    /**
     * @param ObjectEvent $event
     */
    public function onPostSerialize(ObjectEvent $event)
    {
        // getSubscribedEvents doesn't seem to support parent classes
        if (!$event->getObject() instanceof MediaInterface) {
            return;
        }

        $thumbnail = $this->pool->getProvider($event->getObject()->getProvider())
            ->getThumb($event->getObject());

        $small = $this->cacheManager->getBrowserPath($thumbnail, 'medialibrary');
        $event->getVisitor()->addData('images', ['sm' => $small]);
    }
}
