<?php

namespace Opifer\ContentBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @Route(
     *     "/layouts",
     *     name="opifer.api.layout",
     *     options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $layouts = $this->get('opifer.content.layout_manager')->getRepository()
            ->findAll();

        $data = $this->get('jms_serializer')->serialize($layouts, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }
}
