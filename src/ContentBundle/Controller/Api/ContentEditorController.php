<?php

namespace Opifer\ContentBundle\Controller\Api;

use Opifer\ContentBundle\Block\ContentBlockAdapter;
use Opifer\ContentBundle\Form\Type\BlockAdapterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Opifer\ContentBundle\Event\ResponseEvent;
use Opifer\ContentBundle\Event\ContentResponseEvent;
use Opifer\ContentBundle\OpiferContentEvents as Events;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Block\BlockManager;

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
     * @param integer $rootVersion
     *
     * @return JsonResponse
     */
    public function viewBlockAction($type, $typeId, $id, $rootVersion)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
        /** @var BlockManager $manager */
        $manager = $this->get('opifer.content.block_manager');

        /** @var Environment $environment */
        $environment = $this->get(sprintf('opifer.content.block_%s_environment', $type));
        $environment->load($typeId, $rootVersion);

        $block = $environment->getBlock($id);
        $service = $manager->getService($block);

        $this->get('opifer.content.twig.content_extension')->setBlockMode('manage');

        return new JsonResponse(['view' => $service->manage($block)->getContent()]);
    }

    /**
     * Creates a new block
     *
     * @param Request $request
     * @param string  $type
     * @param integer $typeId
     * @param integer $ownerId
     * @param integer $rootVersion
     *
     * @return JsonResponse
     */
    public function createBlockAction(Request $request, $type, $typeId, $ownerId, $rootVersion)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        $sort        = $request->request->get('sort');
        $parentId    = $request->request->get('parent');
        $class       = $request->request->get('type');
        $placeholder = (int) $request->request->get('placeholder');
        $data        = $request->request->get('data');
        $data        = json_decode($data, true);

        try {
            $newVersion = $manager->getNewVersion($manager->find($ownerId, (int) $rootVersion));

            if ((int) $rootVersion !== $newVersion) {
                throw new \Exception("Only new versions can be editted. New version is {$newVersion} while you requested {$rootVersion}");
            }

            $block = $manager->createBlock($ownerId, $class, $parentId, $placeholder, $sort, $data, $rootVersion);

            $response = new JsonResponse(['state' => 'created', 'id' => $block->getId()]);
            $response->setStatusCode(201);
            $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_contenteditor_view_block', ['type' => $type, 'typeId' => $typeId, 'id' => $block->getId(), 'rootVersion' => $rootVersion])]);

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
    public function removeBlockAction($id, $rootVersion)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');
        /** @var BlockManager $manager */
        $manager  = $this->get('opifer.content.block_manager');
        $response = new JsonResponse;

        try {
            $newVersion = $manager->getNewVersion($manager->find($id, $rootVersion));

            if ((int) $rootVersion !== $newVersion) {
                throw new \Exception("Only new versions can be editted. New version is {$newVersion} while you requested {$rootVersion}");
            }

            $block = $manager->find($id, $rootVersion);
            $manager->remove($block, $rootVersion);
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
        $rootVersion = (int) $request->request->get('rootVersion');


        try {
            $newVersion = $manager->getNewVersion($manager->find($id, $rootVersion));

            if ($rootVersion !== $newVersion) {
                throw new \Exception("Only new versions can be editted. New version is {$newVersion} while you requested {$rootVersion}");
            }

            $manager->moveBlock($id, $parentId, $placeholder, $sort, $rootVersion);

            $response->setStatusCode(200);
            $response->setData(['state' => 'moved']);
            //        $response->headers->add(['Location' => $this->generateUrl('opifer_content_api_pagemanager_view_block', ['id' => $block->getId()])]);
        } catch (\Exception $e) {
            $response->setStatusCode(500, 'Exception');
            $response->setData(['error' => $e->getMessage()]);
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
        $rootVersion = (int) $request->request->get('version');

        try {
            $newVersion = $manager->getNewVersion($manager->find($id, $rootVersion));

            if ($rootVersion !== $newVersion) {
                throw new \Exception("Only new versions can be published. New version is {$newVersion} while you requested {$rootVersion}");
            }

            $block = $manager->find($id);
            $manager->publish($block, $rootVersion);

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
        $rootVersion = (int) $request->request->get('version');

        try {
            $manager->discardAll($id, $rootVersion);

            $response->setStatusCode(200);
            $response->setData(['state' => 'discarded']);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }
}