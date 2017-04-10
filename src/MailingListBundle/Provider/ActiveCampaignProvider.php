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

    protected $statusMap = [
        1 => Subscription::STATUS_SUBSCRIBED,
        0 => Subscription::STATUS_UNSUBSCRIBED,
    ];

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
        $now = new \DateTime();
        $subscriptions = $this->subscriptionManager->findOutOfSync($list);

        foreach ($subscriptions as $subscription){
            $this->synchronise($subscription);
        }

        $this->remoteToLocal($list, $logger, $now);

        $list->setSyncedAt($now);
        $this->subscriptionManager->saveList($list);
    }

    public function remoteToLocal(MailingList $list, \Closure $logger, $now)
    {
        $i = 0;
        $size = 10;
        for ($page = 1; $page < $size; $page ++) {
            $updates = $this->getClient()->api(sprintf('contact/list?filters[listid]=%d&full=1sort=datetime&sort_direction=DESC&page=%d', $list->getRemoteListId(), $page));

            $i = 0;
            foreach ($updates as $member) {
                if (!is_object($member))
                    continue;
                ++$i;

                $subscription = $this->subscriptionManager->findOrCreate($list, $member->email);
                $subscription->setEmail($member->email);
                $subscription->setStatus($this->statusMap[$member->status]);
                $subscription->setUpdatedAt(new \DateTime($member->sdate));
                $subscription->setSyncedAt($now);

                $this->subscriptionManager->save($subscription);
                $logger(sprintf('Processed updates from ActiveCampain for %s - %d/%d', $member->email, $i, count($updates)));
            }
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
