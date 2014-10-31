<?php

namespace Opifer\ContentBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * Index
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
