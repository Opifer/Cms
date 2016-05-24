<?php

namespace Opifer\MailingListBundle\Provider;

use Symfony\Component\Form\AbstractType;

use Opifer\MailingListBundle\Entity\Subscription;
use Opifer\MailingListBundle\Entity\MailingList;

/**
 * MailingListProviderInterface is the interface implemented by all provider classes.
 */
interface MailingListProviderInterface
{
    /**
     * Returns the human readable name of the provider
     *
     * @return string
     */
    public function getName();

    /**
     * Sync a subscription.
     *
     * @param Subscription $subscription
     *
     * @return string
     */
    public function synchronise(Subscription $subscription);
    
    /**
     * Synchronise a with the remote mailing list.
     *
     * @param MailingList   $list
     * @param \Closure      $logger
     */
    public function synchroniseList(MailingList $list, \Closure $logger);

    /**
     * Returns an array id, name of remote lists
     *
     * @return array
     */
    public function getRemoteLists();

}
