<?php

namespace Opifer\MailingListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;

/**
 * MailingListRepository.
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * Get all the unsynced subscriptions from a mailinglist.
     *
     * @param MailingList $mailingList
     *
     * @return Subscription[]
     */
    public function getUnsyncedByMailinglist(MailingList $mailingList)
    {
        return $this->createQueryBuilder('s')
            ->innerjoin('s.mailingList', 'm')
            ->where('s.mailingList = :mailingList')
            ->andWhere('s.status = :pending OR s.status = :failed')
            ->setParameters([
                'mailingList' => $mailingList,
                'pending' => Subscription::STATUS_PENDING,
                'failed' => Subscription::STATUS_FAILED,
            ])
            ->getQuery()
            ->getResult();
    }
}
