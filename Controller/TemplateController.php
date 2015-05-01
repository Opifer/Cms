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
        if ($request->get('attribute')) {
            $attribute = $this->get('opifer.eav.attribute_manager')->getRepository()
                ->find($request->get('attribute'));

            if ($attribute->getAllowedTemplates()->count() === 0) {
                // Remove attribute from request because we want all templates if this attribute doesn't
                // have any allowed templates.
                $request->query->remove('attribute');
            }
        }

        $templates = $this->get('opifer.eav.template_manager')->getRepository()->findByRequest($request);

        $data = $this->get('jms_serializer')->serialize($templates, 'json');

        return new Response($data, 200, [ 'Content-Type' => 'application/json' ]);
    }


    /**
     * @param Request $request
     * @param         $templateId
     *
     * @return Response
     */
    public function editAction(Request $request, $templateId)
    {
        $templateManager = $this->get('opifer.eav.template_manager');
        $template        = $templateManager->getRepository()->find($templateId);

        $form = $this->createForm('eav_template', $template);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($template);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('crud.new.success'));
        }

        return $this->render('OpiferEavBundle:Template:edit.html.twig', [
            'template' => $template,
            'form'     => $form->createView()
        ]);
    }
}
