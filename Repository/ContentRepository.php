<?php

namespace Opifer\CmsBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentRepository as BaseContentRepository;
use Opifer\CrudBundle\Pagination\Paginator;

/**
 * ContentRepository
 *
 * Because content items are used in different kinds of use cases, please specify
 * the scope of the function inside the function name.
 * For example:
 *
 * findByIds()         => returns all content
 * findPublicByIds()   => returns only public content
 * findPrivateByIds()  => returns only private content
 */
class ContentRepository extends BaseContentRepository
{
    /**
     * Search content by term
     *
     * @param string $term
     *
     * @return ArrayCollection
     */
    public function search($term)
    {
        $qb = $this->_em->createQueryBuilder();

        return $this->createValuedQueryBuilder('c')
            ->leftJoin('c.directory', 'd')
            ->where($qb->expr()->orX(
                $qb->expr()->like('c.title', ':term'),
                $qb->expr()->like('c.description', ':term'),
                $qb->expr()->like('v.value', ':term')
            ))
            ->andWhere('c.active = :active')
            ->andWhere('c.indexable = :indexable')
            ->andWhere('d.searchable = :searchable OR c.directory IS NULL')
            ->setParameter('term', '%'.$term.'%')
            ->setParameter('active', 1)
            ->setParameter('indexable', 1)
            ->setParameter('searchable', 1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Search content by term including nested
     *
     * @param  string $term
     *
     * @return ArrayCollection
     */
    public function searchNested($term)
    {
        $qb = $this->_em->createQueryBuilder();

        return $this->createValuedQueryBuilder('c')
            ->leftJoin('c.directory', 'd')
            ->where($qb->expr()->orX(
                $qb->expr()->like('c.title', ':term'),
                $qb->expr()->like('c.description', ':term'),
                $qb->expr()->like('v.value', ':term'),
                $qb->expr()->like('v.value', ':term_entities')
            ))
            ->andWhere('c.active = :active')
            ->andWhere('c.searchable = :searchable OR c.nestedIn IS NOT NULL')
            ->andWhere('d.searchable = :searchable OR c.directory IS NULL')
            ->setParameter('term', '%'.$term.'%')
            ->setParameter('term_entities', '%'.htmlentities($term).'%')
            ->setParameter('active', 1)
            ->setParameter('searchable', 1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get a querybuilder by request
     *
     * @param Request $request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findPaginatedByRequest(Request $request)
    {
        $qb = $this->createValuedQueryBuilder('c');

        if ($request->get('site_id')) {
            $qb->andWhere("c.site = :site")->setParameter('site', $request->get('site_id'));
        }

        if ($request->get('q')) {
            $qb->andWhere("c.title LIKE :query")->setParameter('query', '%'.$request->get('q').'%');
        } else {
            if ($request->get('directory_id')) {
                $qb->andWhere("c.directory = :directory")->setParameter('directory', $request->get('directory_id'));
            } else {
                $qb->andWhere("c.directory is NULL");
            }
        }

        $qb->orderBy('c.slug');

        $page = ($request->get('p')) ? $request->get('p') : 1;
        $limit = ($request->get('limit')) ? $request->get('limit') : 25;

        return new Paginator($qb, $limit, $page);
    }

    /**
     * Find an anonymously created item by it's ID
     *
     * @param integer $id
     *
     * @return Content
     */
    public function findAnonymouslyCreatedById($id)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->andWhere('c.active = :active')
            ->andWhere('c.author IS NULL')
            ->setParameters(['id' => $id, 'active' => 0])
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    /**
     * Find random content items by template
     *
     * orderBy('RAND()') or something similar is not available within Doctrine's DQL.
     * As a side note: Sorting with ORDER BY RAND() is painfully slow starting with 1000 rows.
     *
     * @param string  $template
     * @param integer $limit
     *
     * @return ArrayCollection
     */
    public function findRandomByTemplate($template, $limit = 10)
    {
        if (!is_string($template)) {
            throw new \InvalidArgumentException('The first parameter of "findRandomByTemplate" should be of type string');
        }

        $count = $this->_em->createQueryBuilder()
            ->select('count(c.id)')
            ->from('OpiferCmsBundle:Content', 'c')
            ->innerJoin('c.valueSet', 'vs')
            ->innerJoin('vs.template', 't')
            ->where('t.name = :template')
            ->setParameter('template', $template)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $offset = rand(0, (int) ($count - $limit) - 1);
        $offset = ($offset && $offset > 0) ? $offset : 0;

        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.valueSet', 'vs')
            ->leftJoin('vs.template', 't')
            ->where('t.name = :template')
            ->andWhere('c.active = :active')
            ->andWhere('c.nestedIn IS NULL')
            ->setParameter('template', $template)
            ->setParameter('active', true)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find the latest created content items
     *
     * @param integer $limit
     *
     * @return ArrayCollection
     */
    public function findLatest($limit = 5)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find the last updated content items
     *
     * @param int $limit
     *
     * @return ArrayCollection
     */
    public function findLastUpdated($limit = 5)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find all active and addressable content items
     *
     * @return ArrayCollection
     */
    public function findActiveAddressable()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.directory', 'directory')
            ->Andwhere('c.active = :active')
            ->setParameter('active', '1')
            ->orderBy('directory.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
