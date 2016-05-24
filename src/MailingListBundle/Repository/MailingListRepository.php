<?php

namespace Opifer\MailingListBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MailingListRepository.
 */
class MailingListRepository extends EntityRepository
{

    public function findHasProviders()
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.provider IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}
