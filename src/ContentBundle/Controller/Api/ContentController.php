<?php

namespace Opifer\ContentBundle\Controller\Api;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Opifer\CmsBundle\Entity\Content;
use Opifer\CmsBundle\Entity\Site;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Environment\Environment;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Model\ContentRepository;
use Opifer\ContentBundle\Serializer\BlockExclusionStrategy;
use Opifer\CmsBundle\Entity\Option;
use Opifer\EavBundle\Entity\OptionValue;
use Opifer\ExpressionEngine\Visitor\QueryBuilderVisitor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
    public function getContents(Request $request, ParamFetcher $paramFetcher)
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

                foreach ($options as $option) {
                    $qbO = $this->get('opifer.content.content_manager')
                        ->getRepository()
                        ->createQueryBuilder('a'.$option->getId());

                    //Create subquery for every option
                    $qbO->leftJoin(sprintf('a%d.valueSet', $option->getId()), sprintf('vs%d', $option->getId()))
                        ->leftJoin(sprintf('vs%d.values', $option->getId()), sprintf('v%d', $option->getId()))
                        ->leftJoin(sprintf('v%d.options', $option->getId()), sprintf('p%d', $option->getId()))
                        ->where(sprintf('p%d.id = :optionId%d', $option->getId(), $option->getId()));

                    $groupedOptions[$option->getAttribute()->getId()][$option->getId()] = $qbO;
                }

                $queryParts = [];
                foreach ($groupedOptions as $groupedOption) {
                    $orX = $qb->expr()->orX();

                    //Create correct sql in function
                    foreach ($groupedOption as $optionId => $option) {
                        $qb->setParameter(sprintf('optionId%d', $optionId), $optionId);
                        $orX->add($qb->expr()->in('a.id', $option->getDQL()));
                    }
                    $queryParts[] = $orX;
                }

                //add created queries to main query
                $andX = $qb->expr()->andX();
                $qb->andWhere($andX->addMultiple($queryParts));
            }

            if ($orderBy = $paramFetcher->get('order_by')) {
                $qb->orderBy('a.'.$orderBy, $paramFetcher->get('direction'));
            }

            // Pagination
            $offset = ($paramFetcher->get('page') - 1) * $paramFetcher->get('limit');
            $qb->setFirstResult($offset);
            $qb->setMaxResults($paramFetcher->get('limit'));

            $qb->andWhere('a.publishAt < :now OR a.publishAt IS NULL')
                ->andWhere('a.active = :active')
                ->setParameter('active', true)
                ->setParameter('now',  new \DateTime());

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

        $stringHelper = $this->container->get('opifer.content.string_helper');
        $context = SerializationContext::create()->setGroups(['Default', 'detail'])->enableMaxDepthChecks();
        $json = $this->get('jms_serializer')->serialize([
            'results' => $items,
            'total_results' => $count
        ], 'json', $context);

        $json = $stringHelper->replaceLinks($json);

        $response->setData(json_decode($json, true));

        return $response;
    }

    /**
     * @ApiDoc()
     *
     * @QueryParam(name="attributes", map=true, description="The attributes on which the content should be related")
     * @QueryParam(name="content", requirements="\d+", description="The content item the results should be related to")
     * @QueryParam(name="direction", description="Define the order direction", default="asc")
     * @QueryParam(name="limit", requirements="\d+", description="The amount of results to return", default="3")
     * @QueryParam(name="order_by", description="Define the order")
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @return ContentInterface[]
     */
    public function getContentRelated(Request $request, ParamFetcher $paramFetcher)
    {
        $repository = $this->get('opifer.content.content_manager')->getRepository();

        /** @var Content $content */
        $content = $repository->find($paramFetcher->get('content'));

        $ids = [];
        if (null !== $paramFetcher->get('attributes')) {
            foreach ($paramFetcher->get('attributes') as $attribute) {
                /** @var OptionValue $value */
                $value = $content->getValueSet()->get($attribute);
                $ids = array_merge($ids, $value->getIds());
            }
        }

        $qb = $repository->createQueryBuilder('c')
            ->leftJoin('c.valueSet', 'vs')
            ->leftJoin('vs.values', 'v')
            ->leftJoin('v.attribute', 'a')
            ->leftJoin('v.options', 'o')
            ->where('a.name IN (:attributes)')->setParameter('attributes', $paramFetcher->get('attributes'))
            ->andWhere('o.id IN (:options)')->setParameter('options', $ids)
            ->andWhere('c.id != :self')->setParameter('self', $paramFetcher->get('content'));

        if ($orderBy = $paramFetcher->get('order_by')) {
            $qb->orderBy('c.'.$orderBy, $paramFetcher->get('direction'));
        }

        $qb->setMaxResults($paramFetcher->get('limit'));
        $items = $qb->getQuery()->getResult();

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

        $stringHelper = $this->container->get('opifer.content.string_helper');
        $context = SerializationContext::create()->setGroups(['Default', 'detail'])->enableMaxDepthChecks();

        $json = $this->get('jms_serializer')->serialize([
            'results' => $items,
        ], 'json', $context);

        $json = $stringHelper->replaceLinks($json);

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
    public function index(Request $request)
    {
        /** @var ContentManager $manager */
        $contentManager = $this->get('opifer.content.content_manager');

        $contents = $contentManager->getRepository()->getContentFromRequest($request);

        $formattedContent = [];
        foreach ($contents as $key => $content) {
            if ($content['num_children'] > 0) {
                $formattedContent[$key]['has_children'] = true;
            } else {
                $formattedContent[$key]['has_children'] = false;
            }
            /** @var Content $content */
            $content = $content[0];
            $formattedContent[$key]['id'] = $content->getId();
            $formattedContent[$key]['site_id'] = $content->getSiteId();
            $formattedContent[$key]['parent_id'] = ($content->getParent()) ? $content->getParent()->getId() : 0;
            $formattedContent[$key]['site_id'] = ($content->getSite()) ? $content->getSite()->getId() : null;
            $formattedContent[$key]['active'] = $content->getActive();
            $formattedContent[$key]['title'] = $content->getTitle();
            $formattedContent[$key]['short_title'] = $content->getShortTitle();
            $formattedContent[$key]['description'] = $content->getDescription();
            $formattedContent[$key]['slug'] = $content->getSlug();
            //$formattedContent[$key]['created_at'] = $content->getCreatedAt()->format('Y-m-d H:i:s');
            //$formattedContent[$key]['updated_at'] = $content->getUpdatedAt()->format('Y-m-d H:i:s');
            $formattedContent[$key]['publish_at'] = $content->getPublishAt()->format('Y-m-d H:i:s');
            $formattedContent[$key]['path'] = '/'.$content->getSlug();
            $formattedContent[$key]['level'] = $content->getLvl();
            $formattedContent[$key]['coverImage'] = '';
            $formattedContent[$key]['content_type']['id'] = ($content->getContentType()) ? $content->getContentType()->getId() : '';
            $formattedContent[$key]['content_type']['name'] = ($content->getContentType()) ? $content->getContentType()->getName() : '';
        }

        $data = [
            'results' => $formattedContent,
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
    public function ids($ids)
    {
        $items = $this->get('opifer.content.content_manager')
            ->getRepository()
            ->findOrderedByIds($ids);

        $stringHelper = $this->container->get('opifer.content.string_helper');
        $contents = $this->get('jms_serializer')->serialize($items, 'json', SerializationContext::create()->setGroups(['list'])->enableMaxDepthChecks());
        $contents = $stringHelper->replaceLinks($contents);

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
    public function view(Request $request, $id, $structure = 'tree')
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
        
        if (null !== $version) {
            if ($this->isGranted('ROLE_EDITOR', $content)) {
                $environment->setDraft(true);
            }
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
            'created_at' => $content->getCreatedAt(),
            'updated_at' => $content->getUpdatedAt(),
            'published_at' => $content->getPublishAt(),
            'title' => $content->getTitle(),
            'shortTitle' => $content->getShortTitle(),
            'description' => $content->getDescription(),
            'slug' => $content->getSlug(),
            'alias' => $content->getAlias(),
            'blocks' => $blocks,
            'attributes' => $content->getPivotedAttributes(),
            'medias' => $content->getMedias(),
        ];

        $stringHelper = $this->container->get('opifer.content.string_helper');
        $json = $this->get('jms_serializer')->serialize($contentItem, 'json', $context);
        $json = $stringHelper->replaceLinks($json);

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
    public function delete($id)
    {
        $this->denyAccessUnlessGranted('CONTENT_DELETE');

        /** @var ContentManager $manager */
        $manager = $this->get('opifer.content.content_manager');
        $content = $manager->getRepository()->find($id);

        //generate new slug so deleted slug can be used again
        $hashedSlug = $content->getSlug().'-'.sha1(date('Y-m-d H:i:s'));

        $content->setSlug($hashedSlug);
        $manager->save($content);

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
    public function duplicate(Request $request)
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

    /**
     * @ApiDoc()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function sites(Request $request)
    {
        $sites = $this->get('opifer.cms.site_manager')->getRepository()->findAll();
        $data = $this->get('jms_serializer')->serialize($sites, 'json');

        $data = [
            'results' => json_decode($data, true),
        ];

        return new JsonResponse($data);
    }
}
