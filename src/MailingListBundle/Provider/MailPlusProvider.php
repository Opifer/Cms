<?php

namespace Opifer\MailingListBundle\Provider;

use Opifer\MailingListBundle\Manager\SubscriptionManager;
use Opifer\MailingListBundle\Entity\Subscription;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class MailPlusProvider implements MailingListProviderInterface
{
    /** @var SubscriptionManager  */
    protected $subscriptionManager;

    /** @var string */
    protected $consumerKey;

    /** @var string */
    protected $consumerSecret;

    public function __construct(SubscriptionManager $subscriptionManager, $consumerKey, $consumerSecret)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    public function sync(array $subscriptions)
    {
        $synched = $failed = 0;

        if (!empty($subscriptions)) {
            $stack = HandlerStack::create();

            $middleware = new Oauth1([
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
                'token' => '',
                'token_secret' => '',
            ]);

            $stack->push($middleware);

            $client = new GuzzleClient([
                'base_uri' => 'https://restapi.mailplus.nl',
                'handler' => $stack,
                'auth' => 'oauth',
                'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            ]);

            foreach ($subscriptions as $subscription) {
                try {
                    $contact = [
                        'update' => true,
                        'purge' => false,
                        'contact' => [
                            'externalId' => $subscription->getId(),
                            'properties' => [
                                'email' => $subscription->getEmail(),
                            ],
                        ],
                    ];

                    $response = $client->post('/integrationservice-1.1.0/contact', ['body' => json_encode($contact)]);

                    if ($response->getStatusCode() == '204') { //Contact added successfully status code
                        $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_SYNCED);
                        ++$synched;
                    } else {
                        $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_FAILED);
                        ++$failed;
                    }
                } catch (\Exception $e) {
                    $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_FAILED);
                    ++$failed;
                }
            }
        }

        return sprintf('Synched %d,failed %d subscriptions of %d total', $synched, $failed, count($subscriptions));
    }
}
