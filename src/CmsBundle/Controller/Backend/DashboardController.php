<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Opifer\CmsBundle\Manager\ContentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

        $crons = $this->getDoctrine()->getRepository('OpiferCmsBundle:Cron')->findAll();

        return $this->render('OpiferCmsBundle:Backend/Dashboard:dashboard.html.twig', [
            'latest_content' => $latestContent,
            'new_content' => $newContent,
            'crons' => $crons
        ]);
    }
}
