<?php

namespace Opifer\MailingListBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\MailingListBundle\Entity\Subscription;

class SubscriptionManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Subscription $subscription
     * @param string       $status
     *
     * @return $this
     */
    public function updateStatus(Subscription $subscription, $status)
    {
        $subscription->setStatus($status);

        $this->em->persist($subscription);
        $this->em->flush($subscription);

        return $this;
    }
}
