<?php

namespace Opifer\ContentBundle\Controller\Api;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Model\ContentRepository;
use Opifer\ContentBundle\Serializer\BlockExclusionStrategy;
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
            ->findOrderedByIds($ids);

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
    public function viewAction(Request $request, $id, $structure = 'tree')
    {
        /** @var ContentRepository $contentRepository */
        $contentRepository = $this->get('opifer.content.content_manager')->getRepository();
        if (is_numeric($id)) {
            $content = $contentRepository->find($id);
        } else {
            // TODO; Move the request by slug call to a separate method
            $content = $contentRepository->findOneBySlug($id);
        }

        $version = $request->query->get('_version');
        $debug = $this->getParameter('kernel.debug');

        $contentDate = $content->getUpdatedAt();
        $templateDate = $content->getTemplate()->getUpdatedAt();

        $date = $contentDate > $templateDate ? $contentDate : $templateDate;

        $response = new JsonResponse();
        $response->setLastModified($date);
        $response->setPublic();

        if (null === $version && false == $debug && $response->isNotModified($request)) {
            // return the 304 Response immediately
            return $response;
        }

        /** @var Environment $environment */
        $environment = $this->get('opifer.content.block_environment');
        $environment->setObject($content);

        if (null !== $version && $this->isGranted('ROLE_ADMIN')) {
            $environment->setDraft(true);
        }

        $environment->load();

        $context = SerializationContext::create()
            ->addExclusionStrategy(new BlockExclusionStrategy($content))
            // ->setGroups(['Default', 'detail'])
        ;

        if ($structure == 'tree') {
            $blocks = $environment->getRootBlocks();
            $context->setGroups(['Default', 'tree', 'detail']);
        } else {
            $blocks = $environment->getBlocks();
            $context->setGroups(['Default', 'detail'])
                ->enableMaxDepthChecks();
        }
        // foreach ($blocks as $block) {
        //     var_dump($environment);
        // }die();

        $contentItem = [
            'id' => $content->getId(),
            'title' => $content->getTitle(),
            'shortTitle' => $content->getShortTitle(),
            'description' => $content->getDescription(),
            'slug' => $content->getSlug(),
            'blocks' => $blocks,
        ];

        $json = $this->get('jms_serializer')->serialize($contentItem, 'json', $context);
        
        $response->setData(json_decode($json, true));

        return $response;
    }

    /**
     * Delete content
     *
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        $manager->remove($content);

        return new JsonResponse(['success' => true]);
    }

    /**
     * Duplicates content based on their id
     *
     * @param Request $request
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        $content = $request->getContent();

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

            $duplicatedContent = $contentManager->duplicate($content);
            $this->getDoctrine()->getManager()->flush($duplicatedContent);

            $duplicatedBlocks = $blockManager->duplicate($content->getBlocks(), $duplicatedContent);

            $duplicatedContent->setBlocks($duplicatedBlocks);
            $this->getDoctrine()->getManager()->flush($duplicatedContent);

            $response->setData(['success' => true]);
        } catch (\Exception $e) {
            $response->setStatusCode(500);
            $response->setData(['error' => $e->getMessage()]);
        }

        return $response;
    }
}
