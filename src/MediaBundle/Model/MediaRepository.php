<?php

namespace Opifer\MediaBundle\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class MediaRepository extends EntityRepository
{
    /**
     * Create the query builder from request
     *
     * @param  Request $request
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderFromRequest(Request $request)
    {
        $qb = $this->createQueryBuilder('m');
        
        if ($request->get('ids')) {
            $ids = explode(',', $request->get('ids'));

            $qb->andWhere('m.id IN (:ids)')->setParameter('ids', $ids);
        }

        if ($request->get('search')) {
            $qb->andWhere('m.name LIKE :term')->setParameter('term', '%' . $request->get('search') . '%');
        }

        if ($request->get('order')) {
            $direction = ($request->get('orderdir')) ? $request->get('orderdir') : 'asc';
            $qb->orderBy('m.'.$request->get('order'), $direction);
        }

        return $qb;
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
}
