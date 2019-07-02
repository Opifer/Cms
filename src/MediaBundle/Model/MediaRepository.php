<?php

namespace Opifer\MediaBundle\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class MediaRepository extends EntityRepository
{
    /**
     * Create the query builder from request.
     *
     * @param Request $request
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderFromRequest(Request $request)
    {
        $qb = $this->createQueryBuilder('m');

        if ($ids = $request->get('ids')) {
            if (!is_array($ids)) {
                $ids = explode(',', $request->get('ids'));
            }

            $qb->andWhere('m.id IN (:ids)')->setParameter('ids', $ids);
        }
        
        $qb->andWhere('m.status = :status')->setParameter('status', Media::STATUS_ENABLED);

        if ($request->get('search')) {
            $qb->andWhere('m.name LIKE :term OR m.reference LIKE :term OR m.alt LIKE :term')->setParameter('term', '%'.$request->get('search').'%');
        }

        if ($request->get('order')) {
            $direction = ($request->get('orderdir')) ? $request->get('orderdir') : 'asc';
            $qb->orderBy('m.'.$request->get('order'), $direction);
        }

        $dir = $request->get('directory');
        if ($dir) {
            $qb->leftJoin('m.directory', 'dir')
                ->andWhere('dir.id = :directory')
                ->setParameter('directory', $dir);
        } else if ($dir === '0') {
            $qb->andWhere('m.directory IS NULL');
        }

        return $qb;
    }

    /**
     * Search media items by a searchterm.
     *
     * @param string $term
     * @param int    $limit
     * @param int    $offset
     * @param array  $orderBy
     *
     * @return array
     */
    public function search($term, $limit, $offset, $orderBy = null)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->where('m.name LIKE :term')
            ->andWhere('m.status IN (:statuses)')
            ->setParameters(array(
                'term' => '%'.$term.'%',
                'statuses' => array(0, 1),
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
     * Find all media items by an array of ids.
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
            ->setParameters([
                'ids' => $ids,
                'status' => 1,
            ])
            ->getQuery()
        ;

        return $query->getResult();
    }
}
