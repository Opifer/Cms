<?php

namespace Opifer\ContentBundle\Controller\Backend;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Entity\DocumentBlock;
use Opifer\ContentBundle\Environment\ContentEnvironment;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Opifer\ContentBundle\Provider\BlockProviderInterface;
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
        /** @var BlockProviderInterface $provider */
        $provider = $this->get('opifer.content.block_provider_pool')->getProvider($owner);
        $object = $provider->getBlockOwner($ownerId);

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setDraft(true)->setObject($object);
        $environment->setBlockMode('manage');

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
        /** @var BlockProviderInterface $provider */
        $provider = $this->get('opifer.content.block_provider_pool')->getProvider($owner);
        $object = $provider->getBlockOwner($ownerId);

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setDraft(true)->setObject($object);
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
        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');
        $block = $manager->find($id, true);

        /** @var BlockServiceInterface $service */
        $service = $manager->getService($block);
        $updatePreview = false; // signals parent window preview from iframe to update preview

        $service->preFormSubmit($block);

        $form = $this->createForm(new BlockAdapterFormType($service), $block);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $service->postFormSubmit($form, $block);

            $manager->save($block, true);
            $updatePreview = true;
        }

        return $this->render($service->getEditView(), [
            'block_service' => $service,
            'block' => $block,
            'form' => $form->createView(),
            'update_preview' => $updatePreview
        ]);
    }
}
