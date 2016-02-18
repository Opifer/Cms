<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\CmsBundle\Manager\ContentManager;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Form\Type\ContentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backend Content Controller
 */
class ContentController extends Controller
{
    /**
     * Index action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render($this->getParameter('opifer_content.content_index_view'));
    }

    /**
     * @param int $type
     * @return Response
     */
    public function typeAction($type)
    {
        $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($type);

        $content = $this->get('opifer.content.content_manager')->getRepository()->findByType($contentType);

        return $this->render($this->getParameter('opifer_content.content_type_view'), [
            'content_type' => $contentType,
            'content' => $content,
        ]);
    }

    /**
     * Select the type of content, the site and the language before actually
     * creating a new content item.
     *
     * @param Request $request
     * @param integer $type
     *
     * @return Response
     */
    public function createAction(Request $request, $type = 0)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');

        if ($type) {
            $contentType = $this->get('opifer.content.content_type_manager')->getRepository()->find($type);
            $content = $this->get('opifer.eav.eav_manager')->initializeEntity($contentType->getSchema());
            $content->setContentType($contentType);
        } else {
            $content = $manager->initialize();
        }

        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Create a new document
            $blockManager = $this->get('opifer.content.block_manager');
            $document = new DocumentBlock();
            $document->setPublish(true);
            $document->setSuper($content->getTemplate()->getBlock());
            $blockManager->save($document);

            $content->setBlock($document);
            $manager->save($content);

            return $this->redirectToRoute('opifer_content_contenteditor_design', [
                'type'    => 'content',
                'id'      => $content->getId(),
                'rootVersion' => 0,
            ]);
        }

        return $this->render($this->getParameter('opifer_content.content_new_view'), [
            'form' => $form->createView(),
        ]);
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

        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($content);
        }

        return $this->render($this->getParameter('opifer_content.content_details_view'), [
            'content' => $content,
            'form' => $form->createView()
        ]);
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


    public function historyAction(Request $request, $type, $id, $version = 0)
    {
        return $this->render($this->getParameter('opifer_content.content_history_view'), array());
    }
}
