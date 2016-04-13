<?php

namespace Opifer\MailingListBundle\Manager;

/**
 * MailingListProviderInterface is the interface implemented by all provider classes.
 */
interface MailingListProviderInterface
{
    /**
     * Sync subsriptions.
     */
    public function sync(array $subscriptions);
}
