<?php

namespace Opifer\MailingListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\MailingListBundle\Entity\MailingList;

/**
 * MailingListRepository.
 */
class MailingListRepository extends EntityRepository
{
    /**
     * @return MailingList[]
     */
    public function findWithProviders()
    {
        return $this->createQueryBuilder('ml')
            ->andWhere('ml.provider IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find the mailinglist by an array of IDs
     *
     * @param array|string $ids
     * @return MailingList[]
     */
    public function findByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        return $this->createQueryBuilder('ml')
            ->andWhere('ml.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
