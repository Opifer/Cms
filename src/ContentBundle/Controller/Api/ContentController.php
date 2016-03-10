<?php

namespace Opifer\ContentBundle\Controller\Api;

use JMS\Serializer\SerializationContext;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    /**
     * Index
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('opifer.content.content_manager')
            ->getPaginatedByRequest($request);

        $contents = $paginator->getCurrentPageResults();

        $contents = $this->get('jms_serializer')->serialize(iterator_to_array($contents), 'json', SerializationContext::create()->setGroups(['list'])->enableMaxDepthChecks());

        $data = [
            'results'       => json_decode($contents, true),
            'total_results' => $paginator->getNbResults()
        ];

        return new JsonResponse($data);
    }

    /**
     * Get a content items by a list of ids
     *
     * @param string $ids
     *
     * @return JsonResponse
     */
    public function idsAction($ids)
    {
        $items = $this->get('opifer.content.content_manager')
            ->getRepository()
            ->findByIds($ids);

        $contents = $this->get('jms_serializer')->serialize($items, 'json', SerializationContext::create()->setGroups(['list'])->enableMaxDepthChecks());

        $data = [
            'results'       => json_decode($contents, true),
            'total_results' => count($items)
        ];

        return new JsonResponse($data);
    }

    /**
     * View
     *
     * @param Request $request
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function viewAction(Request $request, $id)
    {
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        $content = $this->get('jms_serializer')->serialize($content, 'json');

        return new Response($content, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Delete
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        $em = $this->get('doctrine')->getManager();

        $em->remove($content);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }


    /**
     * Duplicates content based on their id.
     *
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draft');

        $content        = $this->get('request')->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true);
        }
        /** @var ContentManagerInterface $contentManager */
        $contentManager = $this->get('opifer.content.content_manager');
        $content        = $contentManager->getRepository()->find($params['id']);
        $response       = new JsonResponse;

        try {
            if ( ! $content) {
                throw $this->createNotFoundException('No content found for id ' . $params['id']);
            }

            /** @var BlockManager $blockManager */
            $blockManager = $this->container->get('opifer.content.block_manager');
            $duplicatedBlocks = $blockManager->duplicate($content->getBlocks());

            $duplicatedContent = $contentManager->duplicate($content);

            foreach ($duplicatedBlocks as $block) {
                $block->setOwner($duplicatedContent);
            }

            $duplicatedContent->setBlocks($duplicatedBlocks);

            $this->getDoctrine()->getManager()->flush();

            $response->setData(['success' => true]);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }
}
