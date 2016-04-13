<?php

namespace Opifer\MailingListBundle\Manager;

/**
 * MailingListProviderInterface is the interface implemented by all provider classes.
 */
interface MailingListProviderInterface
{
    /**
     * Sync subsriptions.
     *
     * @param array $subscriptions
     *
     * @return string
     */
    public function sync(array $subscriptions);
}
