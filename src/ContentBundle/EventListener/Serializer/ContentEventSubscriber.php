<?php

namespace Opifer\ContentBundle\EventListener\Serializer;

use Doctrine\Common\Cache\CacheProvider;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Opifer\ContentBundle\Model\Content;
use Symfony\Component\Routing\RouterInterface;

class ContentEventSubscriber implements EventSubscriberInterface
{
    /** @var CacheManager  */
    private $imageCacheManager;

    /** @var RouterInterface  */
    private $router;

    /** @var CacheProvider */
    protected $cache;

    /**
     * Constructor
     *
     * @param CacheManager $imageCacheManager
     * @param RouterInterface $router
     * @param CacheProvider $cacheProvider
     */
    public function __construct(CacheManager $imageCacheManager, RouterInterface $router, CacheProvider $cacheProvider)
    {
        $this->imageCacheManager = $imageCacheManager;
        $this->router = $router;
        $this->cache = $cacheProvider;
    }

    /**
     * {@inheritdoc}
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
        $object = $event->getObject();

        // getSubscribedEvents doesn't seem to support parent classes
        if (!$object instanceof Content) {
            return;
        }

        if (false !== $coverImage = $this->getCoverImage($object)) {
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
        $key = Content::class.'_'.$content->getId().'_cover_image';

        if (!$image = $this->cache->fetch($key)) {
            $image = $content->getCoverImage();
            if ($image) {
                $image = $this->imageCacheManager->getBrowserPath($image, 'medialibrary');
            }

            $this->cache->save($key, $image, 86400);
        }

        return $image;
    }
}
