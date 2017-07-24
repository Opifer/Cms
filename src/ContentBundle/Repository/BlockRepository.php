<?php

namespace Opifer\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Entity\Content;
use Opifer\ContentBundle\Model\ContentInterface;

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


    public function findByOwner(ContentInterface $content)
    {
        return $this->createQueryBuilder('b')
            ->where('b.content = :id')
            ->orWhere('b.template = :id')
            ->setParameter('id', $content)
            ->getQuery()->getResult();
    }
}
