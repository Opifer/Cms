<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Content Repository
 */
class ContentRepository extends NestedTreeRepository
{
    const CACHE_TTL = 3600;

    /**
     * Used by Elastica to transform results to model
     *
     * @param string $entityAlias
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createValuedQueryBuilder($entityAlias)
    {
        return $this->createQueryBuilder($entityAlias)
            ->select($entityAlias, 'vs', 'v', 'a', 'p', 's')
            ->leftJoin($entityAlias . '.valueSet', 'vs')
            ->leftJoin('vs.schema', 's')
            ->leftJoin('vs.values', 'v')
            ->leftJoin('s.attributes', 'a')
            ->leftJoin('v.options', 'p');
    }

    /**
     * Get a querybuilder by request
     *
     * @param Request $request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderFromRequest(Request $request)
    {
        $qb = $this->createValuedQueryBuilder('c');

        if ($request->get('q')) {
            $qb->leftJoin('c.template', 't');
            $qb->andWhere('c.title LIKE :query OR c.alias LIKE :query OR c.slug LIKE :query OR t.displayName LIKE :query');
            $qb->setParameter('query', '%' . $request->get('q') . '%');
        }

        if ($ids = $request->get('ids')) {
            $ids = explode(',', $ids);

            $qb->andWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        }

        $qb->andWhere('c.deletedAt IS NULL');  // @TODO fix SoftDeleteAble filter

        $qb->orderBy('c.slug');

        return $qb;
    }

    /**
     * Find one by ID
     *
     * @param integer $id
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
     * Find one by slug
     *
     * @param string $slug
     *
     * @return ContentInterface
     */
    public function findOneBySlug($slug)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find one by slug with active status
     *
     * @param string $slug
     *
     * @return ContentInterface
     */
    public function findActiveBySlug($slug)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->where('c.slug = :slug')
            ->andWhere('c.active = :active')
            ->setParameters(['slug' => $slug, 'active' => true])
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find one by alias
     *
     * @param string $alias
     *
     * @return ContentInterface
     */
    public function findOneByAlias($alias)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->where('c.alias = :alias')
            ->setParameter('alias', $alias)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find one by alias with active status
     *
     * @param string $alias
     *
     * @return ContentInterface
     */
    public function findActiveByAlias($alias)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->where('c.alias = :alias')
            ->andWhere('c.active = :active')
            ->setParameters(['alias' => $alias, 'active' => true])
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find an anonymously created item by it's ID
     *
     * @param integer $id
     *
     * @return ContentInterface
     */
    public function findAnonymouslyCreatedById($id)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andWhere('c.active = :active')
            //->andWhere('c.author IS NULL')
            ->setParameters(['id' => $id, 'active' => 0])
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getSingleResult();
    }

    /**
     * Find the latest created content items
     *
     * @param integer $limit
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
     * Find content items by multiple ids
     *
     * @param array $ids
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $items = $this->createValuedQueryBuilder('c')
            ->andWhere('c.id IN (:ids)')->setParameter('ids', $ids)
            ->andWhere('c.deletedAt IS NULL')
            ->getQuery()->useResultCache(true, self::CACHE_TTL)->getResult();

        return $this->sortByArray($items, $ids);
    }

    /**
     * Sort the items by an array of ids
     *
     * @param ArrayCollection $items
     * @param array $order
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
     * @param  int $levels
     * @return array
     */
    public function findByLevels($levels = 1, $ids = array())
    {
        $query = $this->createQueryBuilder('c');

        if ($levels > 1) {
            $levels = $levels - 1;
            $selects = ['c'];
            for ($i = 1; $i <= $levels; $i++) {
                $selects[] = 'c'.$i;
            }

            $query->select($selects);

            for ($i = 1; $i <= $levels; $i++) {
                $previous = ($i-1 == 0) ? '' : ($i-1);
                $query->leftJoin('c'.$previous.'.children', 'c'.$i, 'WITH', 'c'.$i.'.active = :active AND c'.$i.'.showInNavigation = :show');
            }
        }

        if ($ids) {
            $query->andWhere('c.id IN (:ids)')->setParameter('ids', $ids);
        } else {
            $query->andWhere('c.parent IS NULL');
        }

        $query->andWhere('c.active = :active')->setParameter('active', true);
        $query->andWhere('c.showInNavigation = :show')->setParameter('show', true);

        return $query->getQuery()->getArrayResult();
    }
}
