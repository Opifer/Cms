<?php

namespace Opifer\ContentBundle\Controller\Api;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opifer\CmsBundle\Entity\Content;
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
     * @ApiDoc()
     *
     * @QueryParam(name="direction", description="Define the order direction", default="asc")
     * @QueryParam(name="expr", description="A expressionengine expression")
     * @QueryParam(name="ids", map=true, requirements="\d+", description="The list of ids")
     * @QueryParam(name="limit", requirements="\d+", description="The amount of results to return", default="10")
     * @QueryParam(name="options", map=true, description="A list of option ids")
     * @QueryParam(name="order_by", description="Define the order")
     *
     * @return ContentInterface[]
     */
    public function getContentsAction(Request $request, ParamFetcher $paramFetcher)
    {
        if ($ids = $paramFetcher->get('ids')) {
            /** @var Content[] $items */
            $items = $this->get('opifer.content.content_manager')
                ->getRepository()
                ->findOrderedByIds($ids);
        } else {
            $qb = $this->get('opifer.content.content_manager')
                ->getRepository()
                ->createValuedQueryBuilder('c');

            if ($expr = $paramFetcher->get('expr')) {
                $conditions = $this->get('opifer.doctrine_expression_engine')->deserialize($expr);
                if (!empty($conditions)) {
                    /** @var QueryBuilder $qb */
                    $qb = $this->get('opifer.doctrine_expression_engine')->toQueryBuilder($conditions, $this->get('opifer.content.content_manager')->getClass());
                }
            }

            if ($options = $paramFetcher->get('options')) {
                $qb->where('p.id IN (:options)')->setParameter('options', $options);
            }

            if ($orderBy = $paramFetcher->get('order_by')) {
                $qb->orderBy('a.'.$orderBy, $paramFetcher->get('direction'));
            }

            $qb->setMaxResults($paramFetcher->get('limit'));

            $items = $qb->getQuery()->getResult();
        }

        $lastUpdatedAt = null;
        foreach ($items as $item) {
            if ($item->getUpdatedAt() > $lastUpdatedAt) {
                $lastUpdatedAt = $item->getUpdatedAt();
            }
        }

        $response = new JsonResponse();
        $response->setLastModified($lastUpdatedAt);
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $context = SerializationContext::create()->setGroups(['Default', 'detail'])->enableMaxDepthChecks();
        $json = $this->get('jms_serializer')->serialize($items, 'json', $context);

        $response->setData(json_decode($json, true));

        return $response;
    }

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

        $contentItem = [
            'id' => $content->getId(),
            'title' => $content->getTitle(),
            'shortTitle' => $content->getShortTitle(),
            'description' => $content->getDescription(),
            'slug' => $content->getSlug(),
            'blocks' => $blocks,
            'attributes' => $content->getPivotedAttributes(),
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
