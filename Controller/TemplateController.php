<?php

namespace Opifer\EavBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateController extends Controller
{
    /**
     * @Route(
     *     "/templates",
     *     name="opifer_eav_api_template",
     *     options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $templates = $this->get('opifer.eav.template_manager')->getRepository()
            ->findByRequest($request);

        $data = $this->get('jms_serializer')->serialize($templates, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }
}
