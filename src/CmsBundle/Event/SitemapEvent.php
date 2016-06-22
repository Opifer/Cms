<?php

namespace Opifer\CmsBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class SitemapEvent extends Event
{
    /** @var array */
    protected $pages = [];

    /**
     * @param string $loc
     * @param \DateTime $lastmod
     * @param string $changefreq
     * @param int $priority
     */
    public function addPage($loc, \DateTime $lastmod, $changefreq = 'daily', $priority = 1)
    {
        $this->pages[] = [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority
        ];
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param array $pages
     */
    public function setPages(array $pages)
    {
        $this->pages = $pages;
    }
}
