<?php

namespace Opifer\ContentBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use JMS\Serializer\EventDispatcher\PreSerializeEvent;
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
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        // do something
        $event->getContext();
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
     * Finds first available image for listing purposes TODO: this is very inefficient
     *
     * @param Content $content
     *
     * @return string
     */
    public function getCoverImage(Content $content)
    {
        return $content->getCoverImage();
    }
}
