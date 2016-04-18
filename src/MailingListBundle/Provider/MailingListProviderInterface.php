<?php

namespace Opifer\MailingListBundle\Provider;
use Opifer\MailingListBundle\Entity\Subscription;

/**
 * MailingListProviderInterface is the interface implemented by all provider classes.
 */
interface MailingListProviderInterface
{
    /**
     * Sync a subsription.
     *
     * @param Subscription $subscription
     *
     * @return string
     */
    public function sync(Subscription $subscription);
}
