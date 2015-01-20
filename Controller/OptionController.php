<?php

namespace Opifer\EavBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OptionController extends Controller
{
    /**
     * Find options
     *
     * @Route(
     *     "/options",
     *     name="opifer_eav_api_option",
     *     options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if ($request->get('attribute')) {
            $attribute = $this->get('opifer.eav.attribute_manager')->getRepository()
                ->findOneByName($request->get('attribute'));

            $options = $attribute->getOptions();
        } else {
            $options = array();
        }

        return new Response($this->get('jms_serializer')->serialize($options, 'json'), 200, ['Content-Type' => 'application/json']);
    }
}
