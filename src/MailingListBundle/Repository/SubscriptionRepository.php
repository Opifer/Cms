<?php

namespace Opifer\MailingListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Guzzle\Tests\Service\Mock\Command\Sub\Sub;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;

/**
 * MailingListRepository.
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * Finds all subscriptions pending synchronisation.
     *
     * @return Subscription[]
     */
    public function findPendingSynchronisation()
    {
        return $this->createQueryBuilder('s')
            ->innerjoin('s.mailingList', 'm')
            ->andWhere('s.status = :pending OR s.status = :failed')
            ->setParameters([
                'pending' => Subscription::STATUS_PENDING,
                'failed' => Subscription::STATUS_FAILED,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds all subscriptions pending synchronisation for a specific mailinglist.
     *
     * @param MailingList $mailingList
     *
     * @return Subscription[]
     */
    public function findPendingSynchronisationList(MailingList $mailingList)
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

    /**
     * @param MailingList $list
     * @param             $email
     *
     * @return null|Subscription
     */
    public function findInListByEmail(MailingList $list, $email)
    {
        return $this->createQueryBuilder('s')
            ->innerjoin('s.mailingList', 'm')
            ->andWhere('m.id = :list_id')
            ->andWhere('s.email = :email')
            ->setParameters([
                'list_id'   => $list->getId(),
                'email'     => $email,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param MailingList $list
     * @param \DateTime   $since
     *
     * @return array
     */
    public function findInListOutOfSync(MailingList $list)
    {
        return $this->createQueryBuilder('s')
            ->innerjoin('s.mailingList', 'm')
            ->andWhere('m.id = :list_id')
            ->andWhere('s.updatedAt > s.syncedAt OR s.syncedAt IS NULL')
            ->setParameters([
                'list_id'   => $list->getId(),
            ])
            ->getQuery()
            ->getResult();
    }
}
