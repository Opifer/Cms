<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\Event\ContentResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;

class ContentController extends Controller
{
    /**
     * Select the type of content, the site and the language before actually
     * creating a new content item.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function initAction(Request $request)
    {
        $event = new ResponseEvent($request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_INIT, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm('opifer_content_init', []);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $templateId = $form->get('template')->getData()->getId();

            return $this->redirect($this->generateUrl('opifer_content_content_new', [
                'mode' => 'simple',
                'template' => $templateId,
            ]));
        }

        return $this->render('OpiferContentBundle:Content:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * New.
     *
     * @param Request $request
     * @param integer $template
     * @param string  $mode     [simple|advanced]
     *
     * @return Response
     */
    public function newAction(Request $request, $template = 0, $mode = 'simple')
    {
        if ($template == 0) {
            return $this->forward('OpiferContentBundle:Backend/Content:init');
        }

        $event = new ResponseEvent($request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_NEW, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $contentManager = $this->get('opifer.content.content_manager');
        $template = $this->get('opifer.eav.template_manager')->getRepository()->find($template);
        $content = $this->get('opifer.eav.eav_manager')->initializeEntity($template);

        $form = $this->createForm('opifer_content', $content, ['mode' => $mode]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $contentManager->handleNestedContentForm($request, $content);
            $contentManager->save($content);

            // Tell the user everything went well.
            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('content.edit.success',
                    ['%title%' => $content->getTitle()])
            );

            return $this->redirect($this->generateUrl('opifer_content_content_edit', [
                'id' => $content->getId(),
                'mode' => $mode,
            ]));
        }

        return $this->render('OpiferContentBundle:Content:edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
            'mode' => $mode,
        ]);
    }

    /**
     * Edit Action.
     *
     * @param Request $request
     * @param integer $id
     * @param integer $directoryId
     * @param string  $mode    [simple|advanced]
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $directoryId = 0, $mode = 'simple')
    {
        $contentManager = $this->get('opifer.content.content_manager');
        $content = $contentManager->getRepository()->find($id);

        if (!$content) {
            throw $this->createNotFoundException('No content found for id '.$id);
        }

        $event = new ContentResponseEvent($content, $request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_EDIT, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm('opifer_content', $content, ['mode' => $mode]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Set updatedAt for every save as Timestampable does not take Values into account
            $content->setUpdatedAt(new \DateTime('now'));

            $contentManager->handleNestedContentForm($request, $content);
            $contentManager->save($content);

            // Tell the user everything went well.
            $this->get('session')->getFlashBag()->add('success',
                $this->get('translator')->trans('content.edit.success', [ '%title%' => $content->getTitle() ]));

            return $this->redirect($this->generateUrl('opifer_content_content_edit', [
                'id'   => $content->getId(),
                'mode' => $mode,
            ]));
        }

        return $this->render('OpiferContentBundle:Content:edit.html.twig', [
            'content'     => $content,
            'form'        => $form->createView(),
            'mode'        => $mode,
            'directoryId' => $directoryId
        ]);
    }


    /**
     * Index action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $directoryId)
    {
        $event = new ResponseEvent($request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_INDEX, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        return $this->render('OpiferContentBundle:Content:index.html.twig', [ 'directoryId' => $directoryId ]);
    }


    /**
     * @param integer $id Id of content which is about to be duplicated
     *
     * @return Response
     */
    public function duplicateAction($id)
    {
        $contentManager = $this->get('opifer.content.content_manager');
        $content = $contentManager->getRepository()->find($id);

        if (!$content) {
            throw $this->createNotFoundException('No content found for id '.$id);
        }

        $duplicateContentId = $contentManager->duplicate($content);

        return $this->redirect($this->generateUrl('opifer_content_content_edit', [
            'id' => $duplicateContentId,
        ]));
    }
}
