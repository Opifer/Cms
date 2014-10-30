<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    /**
     * Select the type of content, the site and the language before actually
     * creating a new content item
     *
     * @param Request $request
     *
     * @return Response
     */
    public function initAction(Request $request)
    {
        $form = $this->createForm('opifer_content_init', []);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $templateId = $form->get('template')->getData()->getId();

            return $this->redirect($this->generateUrl('opifer_content_content_new', [
                'mode'     => 'simple',
                'template' => $templateId
            ]));
        }

        return $this->render('OpiferContentBundle:Content:new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * New
     *
     * @param  Request $request
     * @param  integer $template
     * @param  string  $mode     [simple|advanced]
     *
     * @return Response
     */
    public function newAction(Request $request, $template = 0, $mode = 'simple')
    {
        if ($template == 0) {
            return $this->forward('OpiferContentBundle:Content:new');
        }

        $template = $this->get('opifer.eav.template_manager')->getRepository()->find($template);
        $content = $this->get('opifer.eav.eav_manager')->initializeEntity($template);

        $form = $this->createForm('opifer_content', $content, ['mode' => $mode]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Save nested content
            foreach ($content->getNestedContentAttributes() as $attribute => $value) {
                $nested = $this->get('opifer.content.content_manager')->saveNestedForm($attribute, $request);
                foreach ($nested as $nestedContent) {
                    $value->addNested($nestedContent);
                    $nestedContent->setNestedIn($value);
                    $em->persist($nestedContent);
                }
            }

            // Save the actual content
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            // Tell the user everything went well.
            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('content.edit.success', ['%title%' => $content->getTitle()])
            );

            return $this->redirect($this->generateUrl('opifer_content_content_edit', [
                'id'     => $content->getId(),
                'mode'   => $mode
            ]));
        }

        return $this->render('OpiferContentBundle:Content:edit.html.twig', [
            'content' => $content,
            'form'    => $form->createView(),
            'mode'    => $mode
        ]);
    }

    /**
     * Edit Action
     *
     * @param Request $request
     * @param integer $id
     * @param string  $mode     [simple|advanced]
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $mode = 'simple')
    {
        $em = $this->getDoctrine()->getManager();

        $content = $em->getRepository($this->container->getParameter('opifer_content.content_class'))
            ->find($id);

        if (!$content) {
            throw $this->createNotFoundException('No content found for id ' . $id);
        }

        $form = $this->createForm('opifer_content', $content, ['mode' => $mode]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Save nested content
            foreach ($content->getNestedContentAttributes() as $attribute => $value) {
                $nested = $this->get('opifer.content.content_manager')->saveNestedForm($attribute, $request);
                foreach ($nested as $nestedContent) {
                    $value->addNested($nestedContent);
                    $nestedContent->setNestedIn($value);
                    $em->persist($nestedContent);
                }
            }

            // Save the actual content
            $em->persist($content);
            $em->flush();

            // Tell the user everything went well.
            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('content.edit.success', ['%title%' => $content->getTitle()])
            );

            return $this->redirect($this->generateUrl('opifer_content_content_edit', [
                'id'     => $content->getId(),
                'mode'   => $mode
            ]));
        }

        return $this->render('OpiferContentBundle:Content:edit.html.twig', [
            'content' => $content,
            'form'    => $form->createView(),
            'mode'    => $mode
        ]);
    }

    /**
     * Index action
     *
     * @param integer $siteId
     * @param integer $directoryId
     * @param string  $locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('OpiferContentBundle:Content:index.html.twig');
    }
}
