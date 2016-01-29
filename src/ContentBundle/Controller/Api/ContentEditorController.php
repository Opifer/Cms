<?php

namespace Opifer\ContentBundle\Controller\Api;

use Imagine\Image\Point;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\ContentBlockAdapter;
use Opifer\ContentBundle\Designer\AbstractDesignSuite;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Environment\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContentEditorController
 *
 * @package Opifer\ContentBundle\Controller\Api
 */
class ContentEditorController extends Controller
{
    /**
     * Retrieve the manage view for a block
     *
     * @param string  $type
     * @param integer $typeId
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function viewBlockAction($type, $typeId, $id)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        /** @var Environment $environment */
        $environment = $this->get(sprintf('opifer.content.block_%s_environment', $type));
        $version = $manager->getNewVersion($id);

        $environment->load($typeId)->setVersion($version);

        $block = $environment->getBlock($id);

        $environment->setBlockMode('manage');

        $service = $manager->getService($block);

        $this->get('opifer.content.twig.content_extension')->setBlockEnvironment($environment);


        return new JsonResponse(['view' => $service->manage($block)->getContent()]);
    }

    /**
     * Creates a new block
     *
     * @param Request $request
     * @param string  $type
     * @param integer $typeId
     * @param integer $ownerId
     *
     * @return JsonResponse
     */
    public function createBlockAction(Request $request, $type, $typeId, $ownerId)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        $sort        = $request->request->get('sort');
        $parentId    = $request->request->get('parent');
        $className   = $request->request->get('className');
        $placeholder = (int) $request->request->get('placeholder');
        $data        = $request->request->get('data');
        $data        = json_decode($data, true);

        try {
            $block = $manager->createBlock($ownerId, $className, $parentId, $placeholder, $sort, $data);

            $response = new JsonResponse(['state' => 'created', 'id' => $block->getId()]);
            $response->setStatusCode(201);
            $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_contenteditor_view_block', ['type' => $type, 'typeId' => $typeId, 'id' => $block->getId()])]);

        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * Removes a block
     *
     * @param integer $id
     * @param integer $rootVersion
     *
     * @return JsonResponse
     */
    public function removeBlockAction($id)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        try {
            $block = $manager->find($id);
            $manager->remove($block);
            $response->setData(['state' => 'removed']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }


    /**
     * Creates a new block
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moveBlockAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        $id          = (int) $request->request->get('id');
        $parentId    = (int) $request->request->get('parent');
        $sort        = $request->request->get('sort');
        $placeholder = (int) $request->request->get('placeholder');

        try {
            $manager->moveBlock($id, $parentId, $placeholder, $sort);

            $response->setStatusCode(200);
            $response->setData(['state' => 'moved']);
        } catch (\Exception $e) {
            $response->setStatusCode(500, 'Exception');
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * Makes a block shared and created a PointerBlock in its place
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function makeSharedAction(Request $request, $type, $typeId, $ownerId)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        $response = new JsonResponse;
        $id       = (int)$request->request->get('id');

        try {
            /** @var PointerBlock $pointerBlock */
            $pointerBlock = $manager->makeBlockShared($id);

            $response->setStatusCode(200);
            $response->setData(['state' => 'created', 'id' => $pointerBlock->getId()]);
            $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_contenteditor_view_block', ['type' => $type, 'typeId' => $typeId, 'id' => $pointerBlock->getId()])]);
        } catch (\Exception $e) {
            $response->setStatusCode(500, 'Exception');
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * Publishes a shared block and it's members
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function publishSharedAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;
        $id       = (int) $request->request->get('id');

        try {
            $block = $manager->find($id);
            $manager->publish($block);

            $response->setStatusCode(200);
            $response->setData(['state' => 'published']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage() . $e->getTraceAsString()]);
        }

        return $response;
    }

    /**
     * Publishes a block and it's members
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function publishBlockAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;
        $id          = (int) $request->request->get('id');
        $version     = (int) $request->request->get('version');
        $type        = $request->request->get('type');
        $typeId      = (int) $request->request->get('typeId');

        try {
            $block = $manager->find($id);
            $manager->publish($block);

            /** @var AbstractDesignSuite $suite */
            $suite = $this->get(sprintf('opifer.content.%s_design_suite', $type));
            $suite->load($typeId, $manager->getNewVersion($block))->postPublish();

            $response->setStatusCode(200);
            $response->setData(['state' => 'published']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage() . $e->getTraceAsString()]);
        }

        return $response;
    }


    /**
     * Discards all changes to block and it's members
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function discardBlockAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        $response = new JsonResponse;
        $id = (int) $request->request->get('id');

        try {
            $manager->discardAll($id);

            $response->setStatusCode(200);
            $response->setData(['state' => 'discarded']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }
}