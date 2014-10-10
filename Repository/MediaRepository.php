<?php

namespace Opifer\MediaBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Pagination\Paginator;

class MediaRepository extends EntityRepository
{
    /**
     * Find media by request
     *
     * @param Request $request
     *
     * @return \Opifer\CrudBundle\Pagination\Paginator
     */
    public function findPaginatedByRequest(Request $request)
    {
        $qb = $this->createQueryBuilder('m');

        if ($request->get('ids')) {
            $ids = explode(',', $request->get('ids'));

            $qb->andWhere('m.id IN (:ids)')->setParameter('ids', $ids);
        }

        if ($request->get('search')) {
            $qb->andWhere('m.name LIKE :term')->setParameter('term', '%' . $request->get('search') . '%');
        }

        return new Paginator($qb, $request->get('limit', 100), $request->get('page', 1));
    }

    /**
     * Search media items by a searchterm
     *
     * @param string  $term
     * @param integer $limit
     * @param integer $offset
     * @param array   $orderBy
     *
     * @return array
     */
    public function search($term, $limit, $offset, $orderBy = null)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->where('m.name LIKE :term')
            ->andWhere('m.status IN (:statuses)')
            ->setParameters(array(
                'term'     => '%' . $term . '%',
                'statuses' => array(0, 1)
            )
        );

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all media items by an array of ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function findByIds($ids)
    {
        $query = $this->createQueryBuilder('m')
            ->where('m.id IN (:ids)')
            ->andWhere('m.status = :status')
            ->setParameters(array(
                'ids'    => $ids,
                'status' => 1
            ))
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Reduce queries when retrieving resources with tags.
     *
     * @return array
     */
    public function findActiveWithTags($q)
    {
        $query = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.status = :status')
            ->andWhere('m.name LIKE :q')
            ->setParameters(array(
                'q' => '%' . $q .'%',
                'status' => 1,
            ))
            ->getQuery()
        ;

        return $query;
    }
}
