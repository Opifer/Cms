<?php

namespace Opifer\MailingListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\MailingListBundle\Entity\Subscription;

/**
 * MailingListRepository
 *
 */
class SubscriptionRepository extends EntityRepository
{

	public function getNotSynchedSubscriptionsByMailingList($mailingListId = null){
		return $this->createQueryBuilder('s')
					->select('s')
					->innerjoin('s.mailingList', 'm', 'WITH', 's.mailingList = m.id')
                    ->where("m.id = :mailingListId")
                    ->andWhere("s.status = :status1 OR s.status = :status2")
                    ->setParameters([
                    	"mailingListId" => $mailingListId,
                    	"status1" => Subscription::STATUS_PENDING,
                    	"status2" => Subscription::STATUS_FAILED
                    ])
                    ->getQuery()
                    ->getResult();
	}
}
