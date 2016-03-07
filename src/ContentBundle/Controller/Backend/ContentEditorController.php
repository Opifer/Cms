<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Environment\ContentEnvironment;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentEditorController
 *
 * @package Opifer\ContentBundle\Controller\Backend
 */
class ContentEditorController extends Controller
{

    /**
     * Graphical Content editor
     *
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return Response
     */
    public function designAction($owner, $ownerId)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $blockManager */
        $blockManager = $this->get('opifer.content.block_manager');

        /** @var AbstractDesignSuite $suite */
        $suite = $this->get(sprintf('opifer.content.%s_design_suite', $owner));
        $suite->load($ownerId);

        $parameters = [
            'manager' => $blockManager,
            'toolset' => $blockManager->getToolset(),
            'id' => $suite->getSubject()->getId(),
            'owner' => $owner,
            'title' => $suite->getTitle(),
            'caption' => $suite->getCaption(),
            'permalink' => $suite->getPermalink(),
            'url_properties' => $suite->getPropertiesUrl(),
            'url_cancel' => $suite->getCancelUrl(),
            'url' => $suite->getCanvasUrl(),
        ];

        return $this->render($this->getParameter('opifer_content.content_design_view'), $parameters);
    }


    /**
     * Table of Contents tree
     *
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return Response
     */
    public function tocAction($owner, $ownerId)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->load($owner, $ownerId);

        $twigAnalyzer = $this->get('opifer.content.twig_analyzer');

        $parameters = [
            'environment' => $environment,
            'analyzer' => $twigAnalyzer,
            'object' => $environment->getObject(),
        ];

        return $this->render('OpiferContentBundle:Content:toc.html.twig', $parameters);
    }

    /**
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return mixed
     */
    public function viewAction($owner, $ownerId)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->load($owner, $ownerId);
        $environment->setBlockMode('manage');

        return $this->render($environment->getView(), $environment->getViewParameters());
    }

    /**
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function editBlockAction(Request $request, $id)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');
        $version = $manager->getNewVersion($manager->find($id));

//        if ((int) $rootVersion !== $newVersion) {
//            throw new \Exception("Only new versions can be editted. New version is {$newVersion} while you requested {$rootVersion}");
//        }

        $block = $manager->find($id);

        /** @var BlockServiceInterface $service */
        $service = $manager->getService($block);
        $block->setRootVersion($version);

        $updatePreview = false; // signals parent window preview from iframe to update preview

        $service->preFormSubmit($block);

        $form = $this->createForm(new BlockAdapterFormType($service), $block);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $service->postFormSubmit($form, $block);

            $manager->save($block);
            $updatePreview = true;
        }

        return $this->render($service->getEditView(), [
            'block_service' => $service,
            'block' => $block,
            'form' => $form->createView(),
            'update_preview' => $updatePreview
        ]);
    }

//    /**
//     * @param integer $id
//     * @param integer $current
//     * @param integer $published
//     *
//     * @return Response
//     */
//    public function versionPickerAction($id, $current, $published = 0)
//    {
//        if ($this->getDoctrine()->getManager()->getFilters()->isEnabled('draftversion')) {
//            $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
//        }
//
//        /** @var BlockManager $manager */
//        $manager = $this->get('opifer.content.block_manager');
//        $block   = $manager->find($id);
//
//        $logEntries = $manager->getRootVersions($block);
//
//        return $this->render('OpiferContentBundle:Editor:version_picker.html.twig', ['logentries' => $logEntries, 'current' => $current, 'published' => $published]);
//    }
}
