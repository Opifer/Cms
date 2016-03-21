<?php

namespace Opifer\MediaBundle\Twig;

use Opifer\MediaBundle\Model\Media;
use Opifer\MediaBundle\Provider\Pool;

class MediaExtension extends \Twig_Extension
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * Constructor.
     *
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            'media_source_url' => new \Twig_Filter_Method($this, 'sourceUrl'),
        );
    }

    /**
     * Gets the source url of a media item
     *
     * @param Media $media
     *
     * @return \Twig_Markup
     */
    public function sourceUrl($media)
    {
        return new \Twig_Markup(
            $this->pool->getProvider($media->getProvider())->getUrl($media),
            'utf8'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer.media.twig.extension';
    }
}
