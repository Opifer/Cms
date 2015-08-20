<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Opifer\ContentBundle\Form\Type\ContentEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\BlockServiceInterface;

/**
 * Class ContentEditorController
 *
 * @package Opifer\ContentBundle\Controller\Backend
 */
class ContentEditorController extends Controller
{
    /**
     * @param Request $request
     * @param integer $id
     * @param integer $version
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function viewAction(Request $request, $id, $version = 0)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager   = $this->get('opifer.content.block_manager');
        $block     = $manager->find($id, $version);

        /** @var BlockServiceInterface $service */
        $service   = $this->get('opifer.content.block_manager')->getService($block);

        $contentManager = $this->get('opifer.content.content_manager');
        if ($content = $contentManager->getRepository()->findOneBy(['block' => $block])) {
            $service->setView($content->getTemplate()->getView());
        } elseif ($template = $this->getDoctrine()->getRepository('OpiferContentBundle:Template')->findOneBy(['block' => $block])) {
            $service->setView($template->getView());
        } else {
            throw new \Exception('Document block seems to be orphaned, could not generate view.');
        }

        return $service->manage($block);
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $rootVersion
     *
     * @return Response
     */
    public function editBlockAction(Request $request, $id, $rootVersion = 0)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        $block = $manager->find($id, $rootVersion);

        $service = $manager->getService($block);
        $block->setRootVersion($rootVersion);

        $updatePreview = false; // signals parent window preview from iframe to update preview

        $form = $this->createForm(new BlockAdapterFormType($service), $block);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->save($block);
            $updatePreview = true;
        }

        return $this->render($service->getEditView(), ['block_service' => $service, 'block' => $block, 'form' => $form->createView(), 'update_preview' => $updatePreview]);
    }

//    /**
//     * @param Request $request
//     * @param integer $id
//     * @param integer $current
//     *
//     * @return Response
//     */
//    public function versionDialogAction(Request $request, $id, $current)
//    {
//        $versions = array();
//
//        return $this->render('OpiferContentBundle:Editor:version_dialog.html.twig', ['versions' => $versions, 'current' => $current]);
//    }

    /**
     * @param integer $id
     * @param integer $current
     * @param integer $published
     *
     * @return Response
     */
    public function versionPickerAction($id, $current, $published = 0)
    {
        if ($this->getDoctrine()->getManager()->getFilters()->isEnabled('draftversion')) {
            $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
        }

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');
        $block   = $manager->find($id);

        $logEntries = $manager->getRootVersions($block);

        return $this->render('OpiferContentBundle:Editor:version_picker.html.twig', ['logentries' => $logEntries, 'current' => $current, 'published' => $published]);
    }
}
