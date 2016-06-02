<?php

namespace Opifer\MailingListBundle\Provider;

use DrewM\MailChimp\MailChimp;
use Opifer\CmsBundle\Manager\ConfigManager;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;
use Opifer\MailingListBundle\Manager\SubscriptionManager;

class MailChimpProvider implements MailingListProviderInterface
{
    const API_KEY_SETTING = 'mailchimp_api_key';

    /** @var SubscriptionManager  */
    protected $subscriptionManager;

    /** @var ConfigManager */
    protected $configManager;

    /** @var MailChimp */
    protected $client;

    protected $statusMap = [
        'pending' => Subscription::STATUS_PENDING,
        'subscribed' => Subscription::STATUS_SUBSCRIBED,
        'unsubscribed' => Subscription::STATUS_UNSUBSCRIBED,
        'cleaned' => Subscription::STATUS_CLEANED,
    ];

    /**
     * Constructor.
     *
     * @param SubscriptionManager $subscriptionManager
     * @param ConfigManager       $configManager
     */
    public function __construct(SubscriptionManager $subscriptionManager, ConfigManager $configManager)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->configManager = $configManager;
    }

    public function client()
    {
        if ($this->client == null) {
            $apiKey = $this->configManager->get(self::API_KEY_SETTING);

            if (!$apiKey) {
                throw new \Exception('No MailChimp api key found, please enter your api key in admin › configuration');
            }

            $this->client = new MailChimp($apiKey);
        }

        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'MailChimp';
    }

    public function synchronise(Subscription $subscription)
    {
        // TODO: Implement sync() method.
    }

    public function getRemoteLists()
    {
        $results = $this->client()->get('lists');

        $lists = array();

        foreach ($results['lists'] as $result) {
            $lists[] = ['id' => $result['id'], 'name' => $result['name']];
        }

        return $lists;
    }

    public function synchroniseList(MailingList $list, \Closure $logger)
    {
        $now = new \DateTime();
        $size = 10;
        $lastSync = ($list->getSyncedAt()) ? $list->getSyncedAt()->format('Y-m-d H:i:s') : null;

        $updates = $this->client()->get(sprintf('lists/%s/members', $list->getRemoteListId()), [
            'since_last_changed' => $lastSync,
            'offset' => 0,
            'count' => $size,
        ]);

        $count = $updates['total_items'];

        $i = 0;
        for ($offset = 0; $offset < $count; $offset += $size) {
            if ($offset > 0) {
                $updates = $this->client()->get(sprintf('lists/%s/members', $list->getRemoteListId()), [
                    'since_last_changed' => $lastSync,
                    'offset' => $offset - 1,
                    'count' => $size,
                ]);
                $count = $updates['total_items'];
            }

            foreach ($updates['members'] as $member) {
                ++$i;

                $subscription = $this->subscriptionManager->findOrCreate($list, $member['email_address']);
                $subscription->setEmail($member['email_address']);
                $subscription->setStatus($this->statusMap[$member['status']]);
                $subscription->setUpdatedAt(new \DateTime($member['last_changed']));
                $subscription->setSyncedAt($now);

                $this->subscriptionManager->save($subscription);
                $logger(sprintf('Processed updates from MailChimp for %s - %d/%d', $member['email_address'], $i, $updates['total_items']));
            }
        }

        $subscriptions = $this->subscriptionManager->findOutOfSync($list, $now);

        $i = 0;
        foreach ($subscriptions as $subscription) {
            ++$i;

            $this->client()->post(sprintf('lists/%s/members', $list->getRemoteListId()), [
                'email_address' => $subscription->getEmail(),
                'status' => array_search($subscription->getStatus(), $this->statusMap),
            ]);

            $logger(sprintf('Processed updates to MailChimp for %s - %d/%d',  $subscription->getEmail(), $i, count($subscriptions)));
        }

        $list->setSyncedAt($now);
        $this->subscriptionManager->saveList($list);
    }
}
