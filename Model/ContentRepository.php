<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContentRepository
 *
 * Because content items are used in different kinds of usecases, please specify
 * the scope of the function inside the function name.
 * For example:
 *
 * findByIds()         => returns all content
 * findPublicByIds()   => returns only public content
 * findPrivateByIds()  => returns only private content
 */
class ContentRepository extends EntityRepository
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
            ->select($entityAlias, 'vs', 'v', 'a', 'p')
            ->leftJoin($entityAlias . '.valueSet', 'vs')
            ->leftJoin('vs.values', 'v')
            ->leftJoin('v.attribute', 'a')
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
            $qb->leftJoin('vs.template', 't');
            $qb->andWhere('c.title LIKE :query OR c.alias LIKE :query OR c.slug LIKE :query OR t.displayName LIKE :query')->setParameter('query', '%' . $request->get('q') . '%');
        } else {
            if ($request->get('directory_id')) {
                $qb->andWhere('c.directory = :directory')->setParameter('directory', $request->get('directory_id'));
            } else {
                $qb->andWhere('c.directory is NULL');
            }
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
     * Finds all content by a template, created by a user
     *
     * @param integer $user
     * @param string $template
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findUserContentByTemplate($user, $template)
    {
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.valueSet', 'vs')
            ->innerJoin('vs.template', 't')
            ->where('c.author = :user')
            ->andWhere('t.name = :template')
            ->setParameters([
                'user' => $user,
                'template' => $template
            ])
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getResult();
    }

    /**
     * Find attributed in directory
     *
     * @param integer $siteId
     * @param integer $directoryId
     *
     * @return ArrayCollection
     */
    public function findAttributedInDirectory($siteId = 0, $directoryId = 0)
    {
        $query = $this->createValuedQueryBuilder('c')
            ->where('c.site = :site')
            ->setParameter('site', $siteId);

        if ($directoryId) {
            $query->andWhere('c.directory = :directory')
                ->setParameter('directory', $directoryId);
        }

        return $query->getQuery()->useResultCache(true, self::CACHE_TTL)->getResult();
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
     * Find popular content items by template
     *
     * @deprecated will be removed in 1.0
     *
     * @param     $template
     * @param int $limit
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findPopularByTemplate($template, $limit = 10)
    {
        return $this->findRandomByTemplate($template, $limit);
    }

    /**
     * Find random content items by template
     *
     * orderBy('RAND()') or something similar is not available within Doctrine's DQL.
     * As a side note: Sorting with ORDER BY RAND() is painfully slow starting with 1000 rows.
     *
     * @param string $template
     * @param integer $limit
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findRandomByTemplate($template, $limit = 10)
    {
        if (!is_string($template)) {
            throw new \InvalidArgumentException('The first parameter of "findRandomByTemplate" should be of type string');
        }

        $count = 20;

        $offset = rand(0, (int)($count - $limit) - 1);
        $offset = ($offset) ? $offset : 1;

        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.valueSet', 'vs')
            ->leftJoin('vs.template', 't')
            ->where('t.name = :template')
            ->andWhere('c.active = :active')
            ->setParameter('template', $template)
            ->setParameter('active', true)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getResult();
    }

    /**
     * Find latest items by template type
     *
     * @param string $template
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \InvalidArgumentException
     */
    public function findLatestByTemplate($template)
    {
        if (!is_string($template)) {
            throw new \InvalidArgumentException('The first parameter of "findLatestByTemplate" should be of type string');
        }

        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.valueSet', 'vs')
            ->leftJoin('vs.template', 't')
            ->where('t.name = :template')
            ->andWhere('c.active = :active')
            ->setParameter('template', $template)
            ->setParameter('active', true)
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getResult();
    }

    /**
     * Find by template type sorted by title
     *
     * @param string $template
     * @param string $order
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @throws \InvalidArgumentException
     */
    public function findSortedByTemplateTitle($template, $order = 'ASC')
    {
        if (!is_string($template)) {
            throw new \InvalidArgumentException('The first parameter of "findSortedByTemplateTitle" should be of type string');
        }

        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.valueSet', 'vs')
            ->leftJoin('vs.template', 't')
            ->where('t.name = :template')
            ->andWhere('c.active = :active')
            ->setParameter('template', $template)
            ->setParameter('active', true)
            ->orderBy('c.title', $order)
            ->getQuery();

        return $query->useResultCache(true, self::CACHE_TTL)->getResult();
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
     * Find content items by multiple ids
     *
     * @param array $ids
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAddressableByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $items = $this->createValuedQueryBuilder('c')
            ->andWhere('c.id IN (:ids)')->setParameter('ids', $ids)
            ->andWhere('c.deletedAt IS NULL')
            ->getQuery()
            ->useResultCache(true, self::CACHE_TTL)
            ->getResult();

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
}
