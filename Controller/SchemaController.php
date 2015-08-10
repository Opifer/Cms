<?php

namespace Opifer\EavBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SchemaController
 *
 * @package Opifer\EavBundle\Controller
 */
class SchemaController extends Controller
{
    /**
     * @Route(
     *     "/schemas",
     *     name="opifer_eav_api_schema",
     *     options={"expose"=true}
     * )
     * @Method({"GET"})
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if ($request->get('attribute')) {
            $attribute = $this->get('opifer.eav.attribute_manager')->getRepository()
                ->find($request->get('attribute'));

            if ($attribute->getAllowedSchemas()->count() === 0) {
                // Remove attribute from request because we want all schemas if this attribute doesn't
                // have any allowed schemas.
                $request->query->remove('attribute');
            }
        }

        $schemas = $this->get('opifer.eav.schema_manager')->getRepository()->findByRequest($request);

        $data = $this->get('jms_serializer')->serialize($schemas, 'json');

        return new Response($data, 200, [ 'Content-Type' => 'application/json' ]);
    }
}
