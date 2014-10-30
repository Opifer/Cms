<?php

namespace Opifer\ContentBundle\Model;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Pagination\Paginator;

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
            //->select($entityAlias, 'vs', 'v', 'a', 'p')
            ->select($entityAlias, 'vs', 'v', 'a')
            ->leftJoin($entityAlias . '.valueSet', 'vs')
            ->leftJoin('vs.values', 'v')
            ->leftJoin('v.attribute', 'a')
            //->leftJoin('v.options', 'p')
        ;
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
        $qb->where('c.nestedIn IS NULL');

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
     * Finds all content by a template, created by a user
     *
     * @param integer $user
     * @param string  $template
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function findUserContentByTemplate($user, $template)
    {
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.valueSet', 'vs')
            ->innerJoin('vs.template', 't')
            ->where('c.author = :user')
            ->andWhere('t.name = :template')
            ->setParameters([
                'user'     => $user,
                'template' => $template
            ])
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find attributed in directory
     *
     * @param integer $siteId
     * @param integer $directoryId
     * @param string  $locale
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function findAttributedInDirectory($directoryId = 0)
    {
        if ($directoryId) {
            $query->andWhere("c.directory = :directory")
                ->setParameter('directory', $directoryId)
            ;
        }

        return $query->getQuery()->getResult();
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
            ->where("c.id = :id")
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $query->getSingleResult();
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
            ->where("c.slug = :slug")
            ->setParameter('slug', $slug)
            ->getQuery()
        ;

        return $query->getSingleResult();
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
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    /**
     * @todo just retrieves random content items for now
     */
    public function findPopularByTemplate($template)
    {
        return $this->findRandomByTemplate($template);
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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findRandomByTemplate($template, $limit = 10)
    {
        if (!is_string($template)) {
            throw new \InvalidArgumentException('The first parameter of "findRandomByTemplate" should be of type string');
        }

        $count = 20;

        $offset = rand(0, (int) ($count - $limit)-1);
        $offset = ($offset) ? $offset : 1;

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
     * Find related content
     *
     * @param Content $content
     * @param integer $limit
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function findRelated(Content $content, $limit = 10)
    {
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.valueSet', 'vs')
            ->innerJoin('vs.values', 'v')
            ->innerJoin('v.attribute', 'a')
            ->andWhere('c.active = 1')
            ->andWhere('c.nestedIn IS NULL')
            ->andWhere('c.id <> :id')
            ->setParameter('id', $content->getId())
            ->setParameter('city', $city)
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find the latest created content items
     *
     * @param integer $limit
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function findLatest($limit = 5)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.nestedIn IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find content items by multiple ids
     *
     * @param array $ids
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function findByIds($ids)
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.id in (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
        ;

        return $query->getResult();
    }
}
