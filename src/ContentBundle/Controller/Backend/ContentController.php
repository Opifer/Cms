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

        return $this->render($this->getParameter('opifer_content.content_new_view'), [
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

        $content = $contentManager->getRepository()->find($id);


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
            'manager' => $blockManager,
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

        return $this->render($this->getParameter('opifer_content.content_edit_view'), $parameters);
    }

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

        return $this->render($this->getParameter('opifer_content.content_details_view'), [ 'content' => $content, 'form' => $form->createView() ]);
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

        return $this->render($this->getParameter('opifer_content.content_index_view'), [ 'directoryId' => $directoryId ]);
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
}
