<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Content Repository.
 */
class ContentRepository extends NestedTreeRepository
{
    const CACHE_TTL = 3600;

    /**
     * Used by Elastica to transform results to model.
     *
     * @param string $entityAlias
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createValuedQueryBuilder($entityAlias)
    {
        return $this->createQueryBuilder($entityAlias)
            ->select($entityAlias, 'vs', 'v', 'a', 'p', 's')
            ->leftJoin($entityAlias.'.valueSet', 'vs')
            ->leftJoin('vs.schema', 's')
            ->leftJoin('vs.values', 'v')
            ->leftJoin('s.attributes', 'a')
            ->leftJoin('v.options', 'p');
    }

    /**
     * Get a querybuilder by request.
     *
     * @param Request $request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getContentFromRequest(Request $request)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->addSelect('COUNT(children) AS num_children')
            ->leftJoin('c.children', 'children');

        if ($request->get('q')) {
            $qb->leftJoin('c.template', 't');
            $qb->andWhere('c.title LIKE :query OR c.alias LIKE :query OR c.slug LIKE :query OR t.displayName LIKE :query');
            $qb->setParameter('query', '%'.$request->get('q').'%');
        } else if ($request->get('parent_id')) {
            $qb->leftJoin('c.parent', 'p');
            $qb->andWhere('p.id = :parent')->setParameter('parent', $request->get('parent_id'));
        } else {
            $qb->andWhere('c.parent IS NULL');
        }

        if ($ids = $request->get('ids')) {
            $ids = explode(',', $ids);

            $qb->andWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        }

        $qb->andWhere('c.deletedAt IS NULL AND c.layout = :layout');  // @TODO fix SoftDeleteAble & layout filter
        $qb->setParameter('layout', false);

        $qb->setMaxResults($request->get('limit', 25));

        $qb->orderBy('c.slug');
        $qb->groupBy('c.id');

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Find one by ID.
     *
     * @param int $id
     *
     * @return ContentInterface
     */
    public function findOneById($id)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find one by slug.
     *
     * @param string $slug
     *
     * @return ContentInterface
     */
    public function findOneBySlug($slug)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.slug = :slug OR c.alias = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('c.publishAt < :now OR c.publishAt IS NULL')
            ->andWhere('c.active = :active')
            ->andWhere('c.layout = :layout')
            ->setParameter('now', new \DateTime())
            ->setParameter('active', true)
            ->setParameter('layout', false)
            ->setMaxResults(1)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getOneOrNullResult();
    }

    /**
     * Finds a content item by its id or slug.
     *
     * When $allow404 is set to true, it will look for a 404 page
     *
     * @param int|string $idOrSlug
     * @param bool       $allow404
     *
     * @return null|object|ContentInterface
     */
    public function findOneByIdOrSlug($idOrSlug, $allow404 = false)
    {
        if (is_numeric($idOrSlug)) {
            $content = $this->find($idOrSlug);
        } else {
            $content = $this->findOneBySlug($idOrSlug);
        }

        // If no content was found for the passed id, return the 404 page
        if (!$content && $allow404 == true) {
            $content = $this->findOneBySlug('404');
        }

        return $content;
    }

    /**
     * Find one by slug with active status.
     *
     * @param string $slug
     *
     * @return ContentInterface
     */
    public function findActiveBySlug($slug, $host)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->leftJoin('c.site', 'os')
            ->leftJoin('os.domains', 'd')
            ->where('c.slug = :slug')
            ->andWhere('c.active = :active')
            ->andWhere('c.layout = :layout')
            ->andWhere('c.publishAt < :now OR c.publishAt IS NULL')
            ->andWhere('d.domain = :host OR c.site IS NULL')
            ->setParameters([
                'slug' => $slug,
                'active' => true,
                'layout' => false,
                'now' => new \DateTime(),
                'host' => $host
            ])
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * Find one by alias with active status.
     *
     * @param string $alias
     *
     * @return ContentInterface
     */
    public function findActiveByAlias($alias, $host)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->leftJoin('c.site', 'os')
            ->leftJoin('os.domains', 'd')
            ->where('c.alias = :alias')
            ->andWhere('c.active = :active')
            ->andWhere('c.layout = :layout')
            ->andWhere('(c.publishAt < :now OR c.publishAt IS NULL)')
            ->andWhere('d.domain = :host OR c.site IS NULL')
            ->setParameters([
                'alias' => $alias,
                'active' => true,
                'layout' => false,
                'now' => new \DateTime(),
                'host' => $host
            ])
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * Find an anonymously created item by it's ID.
     *
     * @param int $id
     *
     * @return ContentInterface
     */
    public function findAnonymouslyCreatedById($id)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andWhere('c.active = :active')
            ->andWhere('c.layout = :layout')
            //->andWhere('c.author IS NULL')
            ->setParameters([
                'id' => $id,
                'active' => false,
                'layout' => false
            ])
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find the latest created content items.
     *
     * @param int $limit
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findLatest($limit = 5)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getResult();
    }

    /**
     * Find content items by multiple ids.
     *
     * @param array $ids
     *
     * @return Content[]
     */
    public function findByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        if (!$ids) {
            return [];
        }

        return $this->createValuedQueryBuilder('c')
            ->andWhere('c.id IN (:ids)')->setParameter('ids', $ids)
            ->andWhere('c.deletedAt IS NULL')
            ->andWhere('c.active = :active')
            ->andWhere('c.layout = :layout')
            ->andWhere('c.publishAt < :now OR c.publishAt IS NULL')->setParameter('now', new \DateTime())
            ->setParameter('active', true)
            ->setParameter('layout', false)
            ->getQuery()
            ->useResultCache(true, self::CACHE_TTL)
            ->getResult();
    }

    public function findOrderedByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $items = $this->findByIds($ids);

        return $this->sortByArray($items, $ids);
    }

    /**
     * Sort the items by an array of ids.
     *
     * @param ArrayCollection $items
     * @param array           $order
     *
     * @return array
     */
    private function sortByArray($items, array $order)
    {
        $unordered = [];
        foreach ($items as $content) {
            $unordered[$content->getId()] = $content;
        }

        $ordered = [];
        foreach ($order as $id) {
            if (isset($unordered[$id])) {
                $ordered[] = $unordered[$id];
            }
        }

        return $ordered;
    }

    /**
     * Joins and selects the toplevel content items and its children recursively.
     *
     * @param int $levels
     *
     * @return array
     */
    public function findByLevels($levels = 1, $ids = array())
    {
        $query = $this->createQueryBuilder('c');

        if ($levels > 0) {
            $selects = ['c'];
            for ($i = 1; $i <= $levels; ++$i) {
                $selects[] = 'c'.$i;
            }

            $query->select($selects);

            for ($i = 1; $i <= $levels; ++$i) {
                $previous = ($i - 1 == 0) ? '' : ($i - 1);
                $query->leftJoin('c'.$previous.'.children', 'c'.$i, 'WITH', 'c'.$i.'.active = :active AND c'.$i.'.showInNavigation = :show');
            }
        }

        if ($ids) {
            $query->andWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        } else {
            $query->andWhere('c.parent IS NULL');
        }

        $query->andWhere('c.active = :active')->setParameter('active', true);
        $query->andWhere('c.layout = :layout')->setParameter('layout', false);
        $query->andWhere('c.showInNavigation = :show')->setParameter('show', true);

        return $query->getQuery()->getResult();
    }

    /**
     * Find related content to block with value like $search.
     *
     * @param string $term
     * @param string $host
     *
     * @return Content[]
     */
    public function search($term, $host = null, $locale = null)
    {
        $qb = $this->createQueryBuilder('c');

        $results = $qb
            ->innerjoin('c.blocks', 'b', 'WITH', 'c.id = b.content')
            ->leftJoin('c.site', 'os')
            ->leftJoin('os.domains', 'd')
            ->where($qb->expr()->orX(
                $qb->expr()->like('c.title', ':term'),
                $qb->expr()->like('c.description', ':term'),
                $qb->expr()->like('b.value', ':term')
            ))
            ->andWhere('c.searchable = :searchable')
            ->andWhere('c.active = :active')
            ->andWhere('c.layout = :layout')
            ->andWhere('d.domain = :host OR c.site IS NULL')
            ->andWhere('c.locale = :locale OR c.locale IS NULL')
            ->setParameter('term', '%'.$term.'%')
            ->setParameter('searchable', true)
            ->setParameter('active', true)
            ->setParameter('layout', false)
            ->setParameter('host', $host)
            ->setParameter('locale', $locale)
            ->groupBy('c.id')
            ->orderBy('c.id')
            ->getQuery()
            ->getResult();

        return $this->sortSearchResults($results, $term);
    }

    /**
     * Sort search results by giving priority to founded by title.
     *
     * @param array  $results
     * @param string $term
     *
     * @return ArrayCollection
     */
    public function sortSearchResults($results, $term)
    {
        $sortedResults = [];

        if (!empty($results)) {
            foreach ($results as $result) {
                if (stripos($result->getTitle(), $term) !== false) {
                    array_unshift($sortedResults, $result);
                } else {
                    $sortedResults[] = $result;
                }
            }
        }

        return $sortedResults;
    }
}
