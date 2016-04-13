<?php

namespace Opifer\MailingListBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Opifer\MailingListBundle\Entity\Subscription;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class MailPlusProvider implements MailingListProviderInterface
{
    protected $subscriptionManager;

    /** @var Service Container */
    protected $container;

    public function __construct(SubscriptionManager $subscriptionManager, Container $container)
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->container = $container;
    }

    public function sync(array $subscriptions)
    {
        $synched = $failed = 0;
        $logger = $this->container->get('logger');

        if (!empty($subscriptions)) {
            $stack = HandlerStack::create();

            $middleware = new Oauth1([
                'consumer_key' => $this->container->getParameter('opifer_mailing_list.mailplus.consumer_key'),
                'consumer_secret' => $this->container->getParameter('opifer_mailing_list.mailplus.consumer_secret'),
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
                    $logger->addError('MailPlus contact #'.$subscription->getId().' message: '.$e->getMessage());
                    ++$failed;
                }
            }
        }

        return sprintf('Synched %d,failed %d subscriptions of %d total', $synched, $failed, count($subscriptions));
    }
}
