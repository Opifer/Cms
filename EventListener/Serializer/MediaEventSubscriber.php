<?php

namespace Opifer\MediaBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\MediaBundle\Provider\Pool;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use PhpOption\None;

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
     * @var FilterConfiguration
     */
    protected $filterConfig;

    /**
     * Constructor.
     *
     * @param CacheManager $cacheManager
     */

    public function __construct(CacheManager $cacheManager, FilterConfiguration $filterConfig, Pool $pool)
    {
        $this->cacheManager = $cacheManager;
        $this->filterConfig = $filterConfig;
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

        $provider = $this->pool->getProvider($event->getObject()->getProvider());

        if ($provider->getName() == 'image') {
            $reference = $provider->getThumb($event->getObject());

            $groups = $event->getContext()->attributes->get('groups');
            
            if (!$groups instanceof None && in_array('detail', $groups->get())) {
                $filters = array_keys($this->filterConfig->all());
            } else {
                $filters = ['medialibrary'];
            }

            $variants = [];
            foreach ($filters as $filter) {
                $variants[$filter] = $this->cacheManager->getBrowserPath($reference, $filter);
            }

            $event->getVisitor()->addData('images', $variants);
        }

        $event->getVisitor()->addData('original', $provider->getUrl($event->getObject()));
    }
}
