<?php

namespace Opifer\CmsBundle\Controller\Frontend;

use Opifer\CmsBundle\Event\Events;
use Opifer\CmsBundle\Event\SitemapEvent;
use Opifer\ContentBundle\Model\ContentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends Controller
{
    /**
     * Renders the sitemap.
     *
     * @return Response
     */
    public function sitemapAction()
    {
        /* @var ContentInterface[] $content */
        $contents = $this->get('opifer.content.content_manager')->getRepository()->findIndexable();

        $event = new SitemapEvent();

        foreach ($contents as $content) {
            $event->addUrl($this->generateUrl('_content', ['slug' => $content->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL), $content->getUpdatedAt());
        }

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(Events::POPULATE_SITEMAP, $event);

        return $this->render('OpiferCmsBundle:Sitemap:sitemap.xml.twig', [
            'urls' => $event->getUrls(),
        ]);
    }
}
