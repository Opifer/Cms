<?php

namespace Opifer\CmsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var  \Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     * @param EntityManager   $em
     */
    public function __construct(RouterInterface $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * Populate the sitemap.
     *
     * @param SitemapPopulateEvent $event
     */
    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();

        if (is_null($section) || $section == 'default') {
            $contentRepository = $this->em->getRepository('OpiferCmsBundle:Content');
            $content = $contentRepository->findAll();

            foreach ($content as $item) {
                if ($item->isPrivate() || !$item->isIndexable()) {
                    continue;
                }

                // Get absolute content url
                $url = $this->router->generate('_content', ['slug' => $item->getSlug()], true);

                // Add the url to the urlset named default
                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_HOURLY,
                        1
                    ),
                    'default'
                );
            }
        }
    }
}
