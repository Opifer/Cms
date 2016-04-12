<?php

namespace Opifer\MailingListBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManagerInterface;
use Opifer\MailingListBundle\Entity\Subscription;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class SubscriptionManager
{
    /** @var Service Container */
    protected $container;

    public function __construct(EntityManagerInterface $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function addContactToMailPlus(Subscription $subscription)
    {
        if (!empty($subscription)) {
            try {
                $stack = HandlerStack::create();

                $middleware = new Oauth1([
                    'consumer_key' => $this->container->getParameter('opifer_mailinglist.mailplus.consumer_key'),
                    'consumer_secret' => $this->container->getParameter('opifer_mailinglist.mailplus.consumer_secret'),
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
                    return true;
                }
            } catch (\Exception $e) {
                return 'MailPlus contact #'.$contact->getId().' message: '.$e->getMessage();
            }
        }
    }

    /**
     * @param Subscription $subscription
     *
     * @return $this
     */
    public function save(Subscription $subscription)
    {
        $this->em->persist($subscription);
        $this->em->flush($subscription);

        return $this;
    }
}
