<?php

namespace Opifer\MailingListBundle\Provider;

use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;
use Opifer\MailingListBundle\Manager\SubscriptionManager;

class ActiveCampaignProvider implements MailingListProviderInterface
{

    protected $api_url;

    protected $api_key;

    protected $client;

    protected $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager, $api_url, $api_key)
    {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
        $this->subscriptionManager = $subscriptionManager;
    }

    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new \ActiveCampaign($this->api_url, $this->api_key);
        }

        return $this->client;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ActiveCampaign';
    }

    public function synchronise(Subscription $subscription)
    {
        try{
            if (!$this->client) {
                $this->getClient();
            }

            $contact = array(
                "email" => $subscription->getEmail(),
                "p[{$subscription->getMailingList()->getRemoteListId()}]" => $subscription->getMailingList()->getRemoteListId(),
                "status[{$subscription->getMailingList()->getRemoteListId()}]" => 1, // "Active" status
            );

            $contact_sync = $this->client->api("contact/sync", $contact);

            if (!(int)$contact_sync->success) {
                // request failed
                $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_FAILED);
                return false;
            }

            // successful request
            $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_SUBSCRIBED);

            return true;
        } catch (\Exception $e) {
            $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_FAILED);

            return true;
        }
    }

    public function synchroniseList(MailingList $list, \Closure $logger)
    {
        $subscriptions = $this->subscriptionManager->getRepository()->findBy(['mailingList' => $list->getId(), 'syncedAt' => null]);

        foreach ($subscriptions as $subscription){
            $this->synchronise($subscription);
        }
    }

    /**
     * @inheritdoc
     */
    public function getRemoteLists()
    {
        try{
            if (!$this->client) {
                $this->getClient();
            }

            $params = array(
                "limit" => 99,
                "offset" => 0,
                "sort" => 0,
                "filter" => 0,
                "public" => 0,
            );

            $paginator = $this->client->api("list/paginator", $params);
            foreach ($paginator->rows as $result) {
                $campaigns[] = ['id' => $result->id, 'name' => $result->name];
            }

            return $campaigns;
        } catch (\Exception $e) {

            return false;
        }
    }
}
