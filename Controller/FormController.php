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

        $template = $value->getTemplate();
        $entity = $this->get('opifer.eav.eav_manager')->initializeEntity($template);

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

    /**
     * Render a form type
     *
     * @Route(
     *     "/eav/form/render/{attribute}/{id}/{index}",
     *     name="opifer_eav_form_render",
     *     options={"expose"=true}
     * )
     *
     * @param string         $attribute
     * @param integer|string $id
     * @param integer        $index
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(Request $request, $attribute, $id, $index)
    {
        $em = $this->getDoctrine();

        if (is_numeric($id)) {
            $object = $this->container->getParameter('opifer_eav.nestable_class');
            $entity = $em->getRepository($object)->find($id);
        } else {
            $template = $this->get('opifer.eav.template_manager')->getRepository()->find($request->get('template'));

            if (!$template) {
                throw new \Exception(sprintf('No template found with ID %d', $request->get('template')));
            }

            $entity = $this->get('opifer.eav.eav_manager')->initializeEntity($template);
            $id = $template->getName();
        }

        $key = $this->get('opifer.eav.eav_manager')
            ->generateNestedTypeName($attribute, $id, $index, $request->get('parent'));

        $form = $this->createForm(new NestedType($key), $entity);
        $form = $this->render('OpiferEavBundle:Form:render.html.twig', ['form' => $form->createView()]);

        $entity = $this->get('jms_serializer')->serialize($entity, 'json');

        return new JsonResponse([
            'form'    => $form->getContent(),
            'content' => json_decode($entity, true),
            'name'    => $id
        ]);
    }
}
