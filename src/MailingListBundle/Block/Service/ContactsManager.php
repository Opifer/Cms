<?php

namespace Opifer\MailingListBundle\Block\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Opifer\MailingListBundle\Entity\MailingList;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class ContactsManager
{
	/** @var string */
    protected $consumer_key;

    /** @var string */
    protected $consumer_secret;

    /** @var Service Container */
    protected $service_container;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(EntityManagerInterface $em, Container $container, LoggerInterface $logger, $consumer_key, $consumer_secret)
    {
        $this->em = $em;
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function addContactToMailPlus(MailingList $contact)
    {
    	if (!empty($contact)) {

    		try {

		    	$stack = HandlerStack::create();

		        $middleware = new Oauth1([
		            'consumer_key'    => $this->consumer_key,
		            'consumer_secret' => $this->consumer_secret,
		            'token'           => '',
		            'token_secret'    => ''
		        ]);

		        $stack->push($middleware);

		        $client = new GuzzleClient([
		            'base_uri' => 'https://restapi.mailplus.nl',
		            'handler' => $stack,
		            'auth' => 'oauth',
		            'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
		        ]);

		        $contact = [
		            "update" => true,
		            "purge" => false,
		            "contact" => [
		                "externalId"=> $contact->getId(),
		                //"testGroup"=> true,
		                "properties"=> [
		                    "email"=> $contact->getName(),
		                    "firstName"=> $contact->getDisplayName(),
		                    "list1" => [
		                        [
		                            "bit"=> 1,
		                            "enabled"=> true
		                        ],
		                        [
		                            "bit"=> 2,
		                            "enabled"=> true
		                        ]
		                    ]
		                ]
		            ]
		        ];  
		    	
		    	$response = $client->post('/integrationservice-1.1.0/contact', ['body' => json_encode($contact) ]);
		        
		        if($response->getStatusCode() == '204') { //Contact added successfully status code
		        	return true;
		        } else{
		        	return false;
		        }
		    } catch (\Exception $e) {
	            $message = '';
	            
	            $this->logger->addError('MailPlus contact #' . $contact->getId() . ' message: ' . $e->getMessage());
	            return false;
	        }
	    }

        return false;
    }

    /**
     * @param MailingList $contact
     *
     * @return $this
     */
    public function save(MailingList $contact)
    {
        $this->em->persist($contact);
        $this->em->flush($contact);

        return $this;
    }
}