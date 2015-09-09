<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\CmsBundle\Manager\ContentManager;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\ContentBlockAdapter;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;

/**
 * Class ContentController
 *
 * @package Opifer\ContentBundle\Controller\Backend
 */
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
    public function newAction(Request $request)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');
        $event = new ResponseEvent($request);
        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_INIT, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $contentClass = $this->container->getParameter('opifer_content.content_class');
        $content = new $contentClass;
        $form = $this->createForm('opifer_content_details', $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Create a new document
            $blockManager = $this->get('opifer.content.block_manager');
            $document = new DocumentBlock();
            $document->setPublish(true);
            $blockManager->save($document);

            $content->setBlock($document);
            $manager->save($content);

            return $this->redirect($this->generateUrl('opifer_content_content_edit', [
                'id'     => $content->getId(),
                'version' => 0,
            ]));
        }

        return $this->render('OpiferContentBundle:Content:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Graphical Content editor
     *
     * @param Request $request
     * @param integer $id
     * @param integer $version
     *
     * @return Response
     */
    public function editAction(Request $request, $id, $version = 1)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var ContentManager $contentManager */
        $contentManager = $this->get('opifer.content.content_manager');
        /** @var BlockManager $blockManager */
        $blockManager = $this->get('opifer.content.block_manager');

        $content        = $contentManager->getRepository()->find($id);


        if ( ! $content) {
            throw $this->createNotFoundException('No content found for id ' . $id);
        }

        if (!$content->getBlock()) {
            // Create a new document
            $version = 1;
            $document = new DocumentBlock();
            $document->setRootVersion(1);

            $content->setBlock($document);
            $contentManager->save($content);
        }

        if (!$version) {
            $version = $blockManager->getNewVersion($content->getBlock());
            $content->getBlock()->setRootVersion($version);
        }

        $parameters = [
            'block' => $content->getBlock(),
            'id' => $content->getId(),
            'title' => $content->getTitle(),
            'permalink' => $this->generateUrl('_content', ['slug' => $content->getSlug()]),
            'version_current' => $version,
            'version_published' => $content->getBlock()->getVersion(),
            'url_properties' => $this->generateUrl('opifer_content_content_details', ['id' => $content->getId()]),
            'url_cancel' =>  $this->generateUrl('opifer_content_content_index', []),
            'url' => $this->generateUrl('opifer_content_contenteditor_view', ['id' => $content->getBlock()->getId(), 'version' => $version]),
        ];

        return $this->render('OpiferContentBundle:Editor:view.html.twig', $parameters);
    }

//    /**
//     * @param Request $request
//     * @param integer $id
//     * @param integer $version
//     *
//     * @return Response
//     */
//    public function viewAction(Request $request, $id, $version = 0)
//    {
//        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
//
//        $blockManager   = $this->get('opifer.content.block_manager');
//        $contentManager = $this->get('opifer.content.content_manager');
//        $content        = $contentManager->getRepository()->find($id);
//        $block          = $content->getBlock();
//
//        if ($version) {
//            $blockManager->revert($block, $version);
//        }
//
//        /** @var BlockServiceInterface $service */
//        $service        = $this->get('opifer.content.block_manager')->getService($block);
//        $service->setView($content->getTemplate()->getView());
//
//        return $service->manage($block);
//    }

    /**
     * Details action.
     *
     * @param Request $request
     * @param integer $directoryId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction(Request $request, $id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        $form = $this->createForm('opifer_content_details', $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($content);
        }

        return $this->render('OpiferContentBundle:Content:details.html.twig', [ 'content' => $content, 'form' => $form->createView() ]);
    }

    /**
     * Index action.
     *
     * @param Request $request
     * @param integer $directoryId
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
     * Duplicates content based on their id.
     *
     * @param integer $id
     *
     * @return Response
     */
    public function duplicateAction($id)
    {
        $contentManager = $this->get('opifer.content.content_manager');
        $content        = $contentManager->getRepository()->find($id);

        if ( ! $content) {
            throw $this->createNotFoundException('No content found for id ' . $id);
        }

        $duplicateContentId = $contentManager->duplicate($content);

        return $this->redirect($this->generateUrl('opifer_content_content_edit', [
            'id' => $duplicateContentId,
        ]));
    }



//
//
//
//    /**
//     * New
//     *
//     * @param Request $request
//     * @param int     $template
//     * @param string  $mode
//     *
//     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse|Response
//     * @throws \Exception
//     */
//    public function newAction(Request $request, $template = 0, $mode = 'simple')
//    {
//        if ($template == 0) {
//            return $this->forward('OpiferContentBundle:Backend/Content:init');
//        }
//
//        $event = new ResponseEvent($request);
//        $this->get('event_dispatcher')->dispatch(Events::CONTENT_CONTROLLER_NEW, $event);
//        if (null !== $event->getResponse()) {
//            return $event->getResponse();
//        }
//
//        $contentManager = $this->get('opifer.content.content_manager');
//        $template       = $this->get('opifer.eav.schema_manager')->getRepository()->find($template);
//        $content        = $this->get('opifer.eav.eav_manager')->initializeEntity($template);
//
//        $form = $this->createForm('opifer_content', $content, [ 'mode' => $mode ]);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $contentManager->handleNestedContentForm($request, $content);
//            $contentManager->save($content);
//
//            // Tell the user everything went well.
//            $this->get('session')->getFlashBag()->add('success',
//                $this->get('translator')->trans('content.edit.success', [ '%title%' => $content->getTitle() ]));
//
//            return $this->redirect($this->generateUrl('opifer_content_content_edit', [
//                'id'          => $content->getId(),
//                'mode'        => $mode,
//            ]));
//        }
//
//        return $this->render('OpiferContentBundle:Content:edit.html.twig', [
//            'content'     => $content,
//            'form'        => $form->createView(),
//            'mode'        => $mode
//        ]);
//    }
}
