<?php

namespace Opifer\ContentBundle\Controller\Api;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opifer\CmsBundle\Entity\Content;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Model\ContentRepository;
use Opifer\ContentBundle\Serializer\BlockExclusionStrategy;
use Opifer\CmsBundle\Entity\Option;
use Opifer\ExpressionEngine\Visitor\QueryBuilderVisitor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
     * @QueryParam(name="search", description="Search on any field")
     * @QueryParam(name="page", description="Used for pagination", default="1")
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
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
            $count = count($items);
        } else {
            $qb = $this->get('opifer.content.content_manager')
                ->getRepository()
                ->createQueryBuilder('a');

            if ($expr = $paramFetcher->get('expr')) {
                $conditions = $this->get('opifer.doctrine_expression_engine')->deserialize($expr);
                if (!empty($conditions)) {
                    /** @var QueryBuilder $qb */
                    $qb = $this->get('opifer.doctrine_expression_engine')->toQueryBuilder($conditions, $this->get('opifer.content.content_manager')->getClass());
                }
            }

            $exprVisitor = new QueryBuilderVisitor($qb);

            // Adds filtering on a search parameter
            if ($search = $paramFetcher->get('search')) {
                // TODO: Add filtering on possible dynamic attributes
                $value = $exprVisitor->shouldJoin('valueSet.values.value');
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('a.title', ':search'),
                    $qb->expr()->like('a.shortTitle', ':search'),
                    $qb->expr()->like('a.description', ':search'),
                    $qb->expr()->like($value, ':search')
                ))->setParameter('search', '%'.$search.'%');

                // Give the title priority over other matches if no specific order is given
                if (!$orderBy = $paramFetcher->get('order_by')) {
                    $qb->addSelect('(CASE WHEN a.title LIKE \'%'.$search.'%\' THEN 0 ELSE 1 END) AS HIDDEN PRIO');
                    $qb->orderBy('PRIO', 'ASC');
                }
            }

            // Adds filtering on options
            if ($options = $paramFetcher->get('options')) {
                $options = $this->getDoctrine()->getRepository(Option::class)->findByIds($options);

                $groupedOptions = [];
                $prefix = $exprVisitor->shouldJoin('valueSet.values.options.id');

                foreach ($options as $option) {
                    $groupedOptions[$option->getAttribute()->getId()][] = $qb->expr()->eq($prefix, $option->getId());
                }

                foreach ($groupedOptions as $groupedOption) {
                    $orX = $qb->expr()->orX();
                    $qb->andWhere($orX->addMultiple($groupedOption));
                }
            }

            if ($orderBy = $paramFetcher->get('order_by')) {
                $qb->orderBy('a.'.$orderBy, $paramFetcher->get('direction'));
            }

            // Pagination
            $offset = ($paramFetcher->get('page') - 1) * $paramFetcher->get('limit');
            $qb->setFirstResult($offset);
            $qb->setMaxResults($paramFetcher->get('limit'));

            $paginator = new Paginator($qb);

            $count = count($paginator);
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
        $json = $this->get('jms_serializer')->serialize([
            'results' => $items,
            'total_results' => $count
        ], 'json', $context);

        $response->setData(json_decode($json, true));

        return $response;
    }

    /**
     * Index.
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
            'results' => json_decode($contents, true),
            'total_results' => $paginator->getNbResults(),
        ];

        return new JsonResponse($data);
    }

    /**
     * Get a content items by a list of ids.
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
            'results' => json_decode($contents, true),
            'total_results' => count($items),
        ];

        return new JsonResponse($data);
    }

    /**
     * Get a single content item.
     *
     * @param Request $request
     * @param int     $id
     * @param string  $structure
     *
     * @return JsonResponse
     */
    public function viewAction(Request $request, $id, $structure = 'tree')
    {
        $response = new JsonResponse();

        /** @var ContentRepository $contentRepository */
        $contentRepository = $this->get('opifer.content.content_manager')->getRepository();
        $content = $contentRepository->findOneByIdOrSlug($id, true);
        if ($content->getSlug() === '404') {
            // If the original content was not found and the 404 page was returned, set the correct status code
            $response->setStatusCode(404);
        }

        $version = $request->query->get('_version');
        $debug = $this->getParameter('kernel.debug');

        $response->setLastModified($content->getLastUpdateDate());
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
     * Delete content.
     *
     * @param int $id
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
     * Duplicates content based on their id.
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
        $content = $contentManager->getRepository()->find($params['id']);
        $response = new JsonResponse();

        try {
            if (!$content) {
                throw $this->createNotFoundException('No content found for id '.$params['id']);
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
