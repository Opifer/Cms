<?php

namespace Opifer\CmsBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * @return Response
     */
    public function viewAction()
    {
        $latestContent = $this->get('opifer.cms.content_manager')->getRepository()
            ->findLastUpdated(8);

        return $this->render('OpiferCmsBundle:Backend/Dashboard:dashboard.html.twig', [
            'latest_content' => $latestContent,
        ]);
    }
}
