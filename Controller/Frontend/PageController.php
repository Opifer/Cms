<?php

namespace Opifer\CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PageController
 *
 * @package Opifer\CmsBundle\Controller\Front
 */
class PageController extends Controller
{
    /**
     * Render the home page.
     *
     * @return Response
     */
    public function homeAction()
    {
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');

        $content = $this->get('opifer.cms.content_manager')->getRepository()
            ->findOneBySlug('index');

        if ($content) {
            $block = $content->getBlock();

            /** @var BlockServiceInterface $service */
            $service = $manager->getService($block);
            $service->setView($content->getTemplate()->getView());

            return $service->execute($block);
        }

        return $this->render('OpiferCmsBundle:Layout:page.html.twig');
    }
}
