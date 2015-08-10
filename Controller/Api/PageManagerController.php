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
     * @param string  $type
     * @param integer $parentId
     * @param string  $placeholder
     * @param integer $sort
     *
     * @return JsonResponse
     */
    public function createBlockAction(Request $request, $type, $parentId, $placeholder)
    {
        $sort = $request->request->get('sort');
        $manager = $this->get('opifer.content.block_manager');
        $service = $manager->getService($type);

        $parent = $manager->getRepository()->find($parentId);

        $block = $service->createBlock($type);
        $block->setOwner($parent->getOwner());
        $block->setParent($parent);
        $block->setPosition($placeholder);


        $block->setSort(0); // < default, gets recalculated later for entire level
        $block->setLevel(0); // < need to be calculated when putting in the tree

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
            array_map(function ($v) use ($value, $id) {
                return $v == $value ? $id : $v;
            }, $sort);

            $parent->addChild($block);
            $children = $parent->getChildren();
            $children = $manager->reSortBlocksByIds($children, $sort);
            foreach ($children as $child) {
                $manager->save($child);
            }
        }

        $response = new JsonResponse(['state' => 'created']);
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
        $manager->remove($block);

        return new JsonResponse(['state' => 'removed']);
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
        $manager = $this->get('opifer.content.block_manager');
        $block = $manager->getRepository()->find($request->request->get('id'));
        $parent = $manager->getRepository()->find($request->request->get('parent'));
        $position = $request->request->get('placeholder');
        $sort = $request->request->get('sort');

        $block->setPosition($position);

        if ($block->getParent()->getId() !== $parent->getId()) {
            $block->setParent($parent);
            $parent->addChild($block);
        }

        $blocks = $parent->getChildren();
        $blocks = $manager->reSortBlocksByIds($blocks, $sort);

        foreach ($blocks as $block) {
            $manager->save($block);
        }

        $response = new JsonResponse(['state' => 'moved']);
        $response->setStatusCode(200);
//        $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_pagemanager_view_block', ['id' => $block->getId()])]);

        return $response;
    }
}