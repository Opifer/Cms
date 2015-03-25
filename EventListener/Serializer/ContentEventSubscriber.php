<?php

namespace Opifer\ContentBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Opifer\ContentBundle\Model\Content;

/**
 * Class ContentEventSubscriber
 *
 * @package  Opifer\ContentBundle\Serializer
 */
class ContentEventSubscriber implements EventSubscriberInterface
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
        $object = $event->getObject();

        // getSubscribedEvents doesn't seem to support parent classes
        if (!$object instanceof Content) {
            return;
        }

        if (false === $coverImage = $this->getCoverImage($object)) {
            return;
        }

        $coverImage = $this->cacheManager->getBrowserPath($coverImage, 'media_sm');
        $event->getVisitor()->addData('coverImage', $coverImage);
    }

    /**
     * Finds first available image for listing purposes
     *
     * @param Content $content
     *
     * @return string
     */
    public function getCoverImage(Content $content)
    {
        foreach ($content->getValueSet()->getValues() as $value) {
            switch (get_class($value)) {
                case 'Opifer\EavBundle\Entity\NestedValue':
                    foreach ($value->getNested() as $nested) {
                        if (false !== $cv = $this->getCoverImage($nested)) {
                            return $cv;
                        }
                    }
                    break;
                case 'Opifer\EavBundle\Entity\MediaValue':
                    foreach ($value->getMedias() as $media) {
                        return $media->getReference();
                        break;
                    }
                    break;
            }
        }

        return false;
    }
}
