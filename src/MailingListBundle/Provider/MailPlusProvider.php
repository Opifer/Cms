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

    /** @var GuzzleClient */
    protected $client;

    /**
     * Constructor.
     *
     * @param SubscriptionManager $subscriptionManager
     * @param $consumerKey
     * @param $consumerSecret
     */
    public function __construct(SubscriptionManager $subscriptionManager, $consumerKey, $consumerSecret)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * Synchronize a subscription
     *
     * @param Subscription $subscription
     * @return bool
     */
    public function sync(Subscription $subscription)
    {
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

            $response = $this->post('contact', $contact);

            if ($response->getStatusCode() == '204') { //Contact added successfully status code
                $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_SYNCED);

                return true;
            } else {
                $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_FAILED);

                return false;
            }
        } catch (\Exception $e) {
            $this->subscriptionManager->updateStatus($subscription, Subscription::STATUS_FAILED);

            return true;
        }
    }

    /**
     * @param string $method
     * @param array $query
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function get($method, array $query)
    {
        $response = $this->getClient()->get('/integrationservice-1.1.0/'.$method, [
            'query' => $query
        ]);

        return $response;
    }

    /**
     * @param string $method
     * @param array $body
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function post($method, array $body)
    {
        $response = $this->getClient()->post('/integrationservice-1.1.0/'.$method, [
            'body' => json_encode($body)
        ]);

        return $response;
    }

    /**
     * @return GuzzleClient
     */
    public function getClient()
    {
        if (!$this->client) {
            $stack = HandlerStack::create();

            $middleware = new Oauth1([
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
                'token' => '',
                'token_secret' => '',
            ]);

            $stack->push($middleware);

            $this->client = new GuzzleClient([
                'base_uri' => 'https://restapi.mailplus.nl',
                'handler' => $stack,
                'auth' => 'oauth',
                'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            ]);
        }

        return $this->client;
    }
}
