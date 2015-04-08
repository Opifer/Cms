<?php

namespace Opifer\MediaBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

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
     * Constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
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

        $small = $this->cacheManager->getBrowserPath($reference, 'medialibrary');
        $event->getVisitor()->addData('images', ['sm' => $small]);
    }
}
