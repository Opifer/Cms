<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Opifer\CmsBundle\Manager\ContentManager;

class DashboardController extends Controller
{
    /**
     * @return Response
     */
    public function viewAction()
    {
        /** @var ContentManager $contentManager */
        $contentManager = $this->get('opifer.cms.content_manager');

        $latestContent = $contentManager->getRepository()
            ->findLastUpdated(6);

        $newContent = $contentManager->getRepository()
            ->findLastCreated(6);

        $unpublishedContent = $contentManager->getRepository()
            ->findUnpublished(6);

        return $this->render('OpiferCmsBundle:Backend/Dashboard:dashboard.html.twig', [
            'latest_content' => $latestContent,
            'new_content' => $newContent,
            'unpublished_content' => $unpublishedContent,
        ]);
    }
}
