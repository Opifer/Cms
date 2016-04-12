<?php

namespace Opifer\MailingListBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Reservation\ReservationManager;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class SyncSubscriptionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sync:subscriptions')
            ->setDescription('Sync subscriptions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptionManager = $this->getContainer()->get('opifer.subscription_manager');
        $mailingListRep = $this->getContainer()->get('doctrine')->getRepository('OpiferMailingListBundle:MailingList');
        $subscriptionListRep = $this->getContainer()->get('doctrine')->getRepository('OpiferMailingListBundle:Subscription');
        $logger = $this->getCOntainer()->get('logger');
        
        $synched = $failed = 0;
        $subscriptionsToSync = 0;

        $mailingLists = $mailingListRep->findAll();

        if (!empty($mailingLists)) {
            foreach($mailingLists as $mailingList) {
                
                $mailingListSubscriptions = $subscriptionListRep->getNotSynchedSubscriptionsByProvider($mailingList->getId());

                if (!empty($mailingListSubscriptions)) {
                    foreach ($mailingListSubscriptions as $subscription) {
                        $result = $subscriptionManager->addContactToMailPlus($subscription);

                        if ($result == true) {
                            $subscription->setStatus(Subscription::STATUS_SYNCHED);
                            $synched++;
                        } else {
                            $subscription->setStatus(Subscription::STATUS_FAILED);
                            $failed++;
                            $logger->addError($result);
                        }

                        $subscriptionManager->save($subscription);
                        $subscriptionsToSync++;
                    }
                }
            }
        }

        $output->writeln(sprintf('Synched %d,failed %d subscriptions of %d total', $synched, $failed, $subscriptionsToSync));
    }
} 