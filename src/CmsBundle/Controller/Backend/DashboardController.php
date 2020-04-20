<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Opifer\CmsBundle\Entity\Cron;
use Opifer\CmsBundle\Manager\ContentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DashboardController extends Controller
{
    /**
     * @return Response
     */
    public function viewAction()
    {
        $this->denyAccessUnlessGranted('DASHBOARD_INDEX');

        /** @var ContentManager $contentManager */
        $contentManager = $this->get('opifer.cms.content_manager');

        $latestContent = $contentManager->getRepository()
            ->findLastUpdated(6);

        $newContent = $contentManager->getRepository()
            ->findLastCreated(6);

        $crons = $this->getDoctrine()->getRepository(Cron::class)->findAll();

        return $this->render('@OpiferCms/Backend/Dashboard/dashboard.html.twig', [
            'latest_content' => $latestContent,
            'new_content' => $newContent,
            'crons' => $crons
        ]);
    }
}
