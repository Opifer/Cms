<?php

namespace Opifer\MailingListBundle\Provider;

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
