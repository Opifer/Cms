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
        $latestContent = $this->getDoctrine()->getRepository('OpiferCmsBundle:Content')
            ->findLastUpdated(8);

        return $this->render('OpiferCmsBundle:Backend/Dashboard:dashboard.html.twig', [
            'latest_content' => $latestContent,
        ]);
    }
}
