<?php

namespace Opifer\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BlockRepository
 *
 * @package Opifer\ContentBundle\Model
 */
class BlockRepository extends EntityRepository
{
    public function findCached($id)
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->useResultCache(true)
            ->setResultCacheLifetime(86400)
            ->getOneOrNullResult();
    }
}
