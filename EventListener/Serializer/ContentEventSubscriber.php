<?php

namespace Opifer\ContentBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Opifer\ContentBundle\Model\Content;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ContentEventSubscriber
 *
 * @package  Opifer\ContentBundle\EventListener\Serializer
 */
class ContentEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager, RouterInterface $router)
    {
        $this->cacheManager = $cacheManager;
        $this->router = $router;
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

        if (false !== $coverImage = $this->getCoverImage($object)) {
            $coverImage = $this->cacheManager->getBrowserPath($coverImage, 'medialibrary');
            $event->getVisitor()->addData('coverImage', $coverImage);
        }

        $event->getVisitor()->addData('path', $this->router->generate('_content', ['slug' => $object->getSlug()]));
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
