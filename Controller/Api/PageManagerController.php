<?php

namespace Opifer\ContentBundle\Controller\Api;

use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\Event\ContentResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;
use Opifer\ContentBundle\Entity\Block;

/**
 * Class PageManagerController
 *
 * @package Opifer\ContentBundle\Controller\Api
 */
class PageManagerController extends Controller
{
    /**
     * @todo: Refactor so that blocks are viewable from the session block tree
     *
     * Retrieve the manage view for a block
     *
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function viewBlockAction($id)
    {
        $manager = $this->get('opifer.content.block_manager');

        $block = $manager->getRepository()->find($id);
        $service = $manager->getService($block);

        $this->get('opifer.content.twig.content_extension')->setBlockMode('manage');

        return new JsonResponse(['view' => $service->manage($block)->getContent()]);
    }

    /**
     * @todo: Refactor so that blocks are created in the session block tree
     *
     * Creates a new block (in the session)
     *
     * @param Request $request
     * @param string $ownerType
     * @param integer $ownerId
     *
     * @return JsonResponse
     */
    public function createBlockAction(Request $request, $ownerType, $ownerId)
    {
        $sort = $request->request->get('sort');
        $parentId = $request->request->get('parent');
        $type = $request->request->get('type');
        $placeholder = $request->request->get('placeholder');
        $manager = $this->get('opifer.content.block_manager');
        $service = $manager->getService($type);

        if ($ownerType == 'template') {
            $owner = $this->get('doctrine')->getRepository('OpiferContentBundle:Template')->find($ownerId);
        }

        $block = $service->createBlock($type);

        if ($parentId) {
            $parent = $manager->getRepository()->find($parentId);
            $block->setParent($parent);
        }

        $block->setOwner($owner);
        $block->setPosition($placeholder);


        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setLevel(0); // < need to be calculated when putting in the tree

        $data = $request->request->get('data');
        // This should replaced with a more hardened function
        if ($data = json_decode($request->request->get('data'), true)) {
            foreach ($data as $attribute => $value) {
                $method = "set$attribute";
                $block->$method($value);
            }
        }

        $manager->save($block);

        if (count($sort) > 1) {
            $value = "";
            $id = $block->getId();
            array_map(
                function ($v) use ($value, $id) {
                    return $v == $value ? $id : $v;
                },
                $sort
            );

            if ($parentId) {
                $parent->addChild($block);
                $siblings = $parent->getChildren();
            } else {
                $owner->addBlock($block);
                $siblings = $owner->getChildBlocks();
            }
            $siblings = $manager->reSortBlocksByIds($siblings, $sort);

            foreach ($siblings as $sibling) {
                $manager->save($sibling);
            }
        }

        $response = new JsonResponse(['state' => 'created', 'id' => $block->getId()]);
        $response->setStatusCode(201);
        $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_pagemanager_view_block', ['id' => $block->getId()])]);

        return $response;
    }

    /**
     * @todo: Refactor so that blocks are removed from the session block tree
     *
     * Removes a block
     *
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function removeBlockAction($id)
    {
        $manager = $this->get('opifer.content.block_manager');

        $block = $manager->getRepository()->find($id);

        $response = new JsonResponse;
        try {
            $manager->remove($block);
            $response->setData(['state' => 'removed']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }


    /**
     * @todo: Refactor so that blocks are moved in the session block tree
     *
     * Creates a new block (in the session)
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moveBlockAction(Request $request)
    {
        $response = new JsonResponse;

        try {
            $manager = $this->get('opifer.content.block_manager');

            $block = $manager->getRepository()->find(intval($request->request->get('id')));

            if ($parentId = $request->request->get('parent')) {
                $parent = $manager->getRepository()->find($parentId);
            }

            $position = $request->request->get('placeholder');
            $sort = $request->request->get('sort');

            $block->setPosition($position);

            if ($parentId && (!$block->getParent() || $block->getParent()->getId() !== $parent->getId())) {
                $block->setParent($parent);
                $parent->addChild($block);
            } else {
                $block->setParent(null);
            }

            if ($parentId) {
                $siblings = $parent->getChildren();
            } else {
                $siblings = $block->getOwner()->getChildBlocks();
            }

            $siblings = $manager->reSortBlocksByIds($siblings, $sort);

            foreach ($siblings as $sibling) {
                $manager->save($sibling);
            }

            $response->setStatusCode(200);
            $response->setData(['state' => 'moved']);
            //        $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_pagemanager_view_block', ['id' => $block->getId()])]);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }
}