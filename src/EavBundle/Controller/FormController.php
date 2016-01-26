<?php

namespace Opifer\EavBundle\Controller;

use Opifer\EavBundle\Form\Type\NestedType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class FormController extends Controller
{
    /**
     * @Route(
     *     "/eav/form/submit/{valueId}",
     *     name="opifer_eav_form_submit",
     *     options={"expose"=true}
     * )
     * @Method({"POST"})
     *
     * @param Request $request
     * @param integer $valueId
     *
     * @return RedirectResponse
     */
    public function submitAction(Request $request, $valueId)
    {
        $value = $this->getDoctrine()->getRepository('OpiferEavBundle:FormValue')
            ->find($valueId);

        if (!$value) {
            throw new ResourceNotFoundException(sprintf('No value with ID "%s" could be found.', $valueId));
        }

        $schema = $value->getSchema();
        $entity = $this->get('opifer.eav.eav_manager')->initializeEntity($schema);

        $form = $this->createForm('eav_post', $entity, [
            'valueId' => $valueId
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            if($request->isXmlHttpRequest()) {
                $response = [
                    "success" => true,
                    'payload' => [
                        'message' => 'Form was submitted successfully!'
                    ]
                ];

                if($value->getValue()) {
                    $response['payload']['redirect'] = $value->getValue();
                }

                return new JsonResponse($response);
            } elseif (is_null($value->getValue()) || $value->getValue() == '') {
                return new Response('Form was submitted successfully!');
            } else {
                return new RedirectResponse($value->getValue());
            }
        } else {
            foreach ($form->getErrors() as $error) {
                dump($error);
            }
            die;
        }
    }
}
