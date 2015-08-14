<?php

namespace Opifer\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="opifer.cms.dashboard.view")
     *
     * @return Response
     */
    public function viewAction()
    {
        $latestContent = $this->get('opifer.cms.content_manager')->getRepository()
            ->findLastUpdated(8);

        return $this->render('OpiferCmsBundle:Dashboard:dashboard.html.twig', [
            'latest_content' => $latestContent
        ]);
    }
}
