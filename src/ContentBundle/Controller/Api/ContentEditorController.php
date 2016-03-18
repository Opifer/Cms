<?php

namespace Opifer\ContentBundle\Controller\Api;

use Imagine\Image\Point;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\ContentBlockAdapter;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\ClipboardBlockService;
use Opifer\ContentBundle\Designer\AbstractDesignSuite;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Provider\BlockProviderInterface;
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
     * @param string  $owner
     * @param integer $ownerId
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function viewBlockAction($owner, $ownerId, $id)
    {
        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        $object = $this->get('opifer.content.block_provider_pool')->getProvider($owner)->getBlockOwner($ownerId);

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setDraft(true)->setObject($object);

        $block = $environment->getBlock($id);

        $environment->setBlockMode(Environment::MODE_MANAGE);

        /** @var AbstractBlockService $service */
        $service = $manager->getService($block);
        $service->setEnvironment($environment);

        $this->get('opifer.content.twig.content_extension')->setBlockEnvironment($environment);


        return new JsonResponse(['view' => $service->manage($block)->getContent()]);
    }

    /**
     * Creates a new block
     *
     * @param Request $request
     * @param string  $owner
     * @param integer $ownerId
     *
     * @return JsonResponse
     */
    public function createBlockAction(Request $request, $owner, $ownerId)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        $object = $this->get('opifer.content.block_provider_pool')->getProvider($owner)->getBlockOwner($ownerId);

        $sort        = $request->request->get('sort');
        $parentId    = $request->request->get('parent');
        $className   = $request->request->get('className');
        $placeholder = (int) $request->request->get('placeholder');
        $data        = $request->request->get('data');
        $data        = json_decode($data, true);

//        try {
            $block = $manager->createBlock($object, $className, $parentId, $placeholder, $sort, $data);

            $response = new JsonResponse(['state' => 'created', 'id' => $block->getId()]);
            $response->setStatusCode(201);
            $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_contenteditor_view_block', ['owner' => $owner, 'ownerId' => $ownerId, 'id' => $block->getId()])]);

//        } catch (\Exception $e) {
//            $response->setStatusCode(500);
//            $response->setData(['error' => $e->getMessage()]);
//        }

        return $response;
    }

    /**
     * Removes a block
     *
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function removeBlockAction($id)
    {
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        try {
            $block = $manager->find($id, true);
            $manager->remove($block, true);
            $response->setData(['state' => 'removed']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * Copies a reference of a block to the clipboard
     *
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function clipboardBlockAction($id)
    {
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');

        /** @var ClipboardBlockService $clipboardService */
        $clipboardService  = $this->get('opifer.content.clipboard_block');
        $response = new JsonResponse;

        try {
            $block = $manager->find($id, true);
            $clipboardService->addToClipboard($block);
            $blockService = $manager->getService($block);

            $response->setData(['message' => sprintf('%s copied to clipboard', $blockService->getName())]);
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
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        $id          = (int) $request->request->get('id');
        $parentId    = (int) $request->request->get('parent');
        $sort        = $request->request->get('sort');
        $placeholder = (int) $request->request->get('placeholder');

        try {
            $manager->moveBlock($id, $parentId, $placeholder, $sort, true);

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
     * @param         $owner
     * @param         $ownerId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function makeSharedAction(Request $request, $owner, $ownerId)
    {
        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        $response = new JsonResponse;
        $id       = (int)$request->request->get('id');

        try {
            /** @var PointerBlock $pointerBlock */
            $pointerBlock = $manager->makeBlockShared($id);

            $response->setStatusCode(200);
            $response->setData(['state' => 'created', 'id' => $pointerBlock->getId()]);
            $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_contenteditor_view_block', ['owner' => $owner, 'ownerId' => $ownerId, 'id' => $pointerBlock->getId()])]);
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
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

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
    public function publishAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

        $owner        = $request->request->get('owner');
        $ownerId      = (int) $request->request->get('ownerId');

        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        /** @var BlockProviderInterface $provider */
        $provider = $this->get('opifer.content.block_provider_pool')->getProvider($owner);
        $response = new JsonResponse;

        $object = $provider->getBlockOwner($ownerId);

        try {
            $manager->publish($object->getBlocks());

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
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

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