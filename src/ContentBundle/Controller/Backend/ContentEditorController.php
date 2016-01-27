<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Environment\Environment;

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
     * @param Request $request
     * @param string  $type
     * @param integer $version
     *
     * @return Response
     */
    public function designAction(Request $request, $type, $id, $version = 0)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $blockManager */
        $blockManager = $this->get('opifer.content.block_manager');

        /** @var AbstractDesignSuite $suite */
        $suite = $this->get(sprintf('opifer.content.%s_design_suite', $type));
        $suite->load($id, $version);

        if (!$suite->getBlock()) {
            // Create a new document
            $version = 1;
            $document = new DocumentBlock();
            $document->setRootVersion(1);

            $suite->setBlock($document);
            $suite->saveSubject();
        }

        if (!$version) {
            $version = $blockManager->getNewVersion($suite->getBlock());
            $suite->getSubject()->getBlock()->setRootVersion($version);
        }

        $parameters = [
            'manager' => $blockManager,
            'block' => $suite->getBlock(),
            'id' => $suite->getSubject()->getId(),
            'type' => $type,
            'title' => $suite->getTitle(),
            'caption' => $suite->getCaption(),
            'permalink' => $suite->getPermalink(),
            'version_current' => $version,
            'version_published' => $suite->getBlock()->getVersion(),
            'url_properties' => $suite->getPropertiesUrl(),
            'url_cancel' => $suite->getCancelUrl(),
            'url' => $suite->getCanvasUrl($version),
        ];

        return $this->render($this->getParameter('opifer_content.content_edit_view'), $parameters);
    }

    /**
     * @param Request $request
     * @param string  $type
     * @param integer $id
     * @param integer $version
     *
     * @return mixed
     */
    public function viewAction(Request $request, $type, $id, $version = 0)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var Environment $environment */
        $environment = $this->get(sprintf('opifer.content.block_%s_environment', $type));
        $environment->load($id, $version);
        $environment->setBlockMode('manage');

        return $this->render($environment->getView(), $environment->getViewParameters());
    }

    /**
     * @param Request $request
     * @param integer $id
     * @param integer $rootVersion
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function editBlockAction(Request $request, $id, $version = 0)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');
        $version = $manager->getNewVersion($manager->find($id));

//        if ((int) $rootVersion !== $newVersion) {
//            throw new \Exception("Only new versions can be editted. New version is {$newVersion} while you requested {$rootVersion}");
//        }

        $block = $manager->find($id, $version);

        /** @var BlockServiceInterface $service */
        $service = $manager->getService($block);
        $block->setRootVersion($version);

        $updatePreview = false; // signals parent window preview from iframe to update preview

        $service->preFormSubmit($block);

        $form = $this->createForm(new BlockAdapterFormType($service), $block);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $service->postFormSubmit($form, $block);

            $manager->save($block, $version);
            $updatePreview = true;
        }

        return $this->render($service->getEditView(), [
            'block_service' => $service,
            'block' => $block,
            'form' => $form->createView(),
            'update_preview' => $updatePreview
        ]);
    }

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
