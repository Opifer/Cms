<?php

namespace Opifer\MailingListBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Opifer\MailingListBundle\Entity\MailingList;
use Opifer\MailingListBundle\Entity\Subscription;

class SyncSubscriptionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('opifer:sync:subscriptions')
            ->setDescription('Synchronize mailinglist subscriptions');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailingListRep = $this->getContainer()->get('doctrine')->getRepository('OpiferMailingListBundle:MailingList');
        $subscriptionListRep = $this->getContainer()->get('doctrine')->getRepository('OpiferMailingListBundle:Subscription');

        $mailingLists = $mailingListRep->findAll();

        if (!empty($mailingLists)) {
            foreach ($mailingLists as $mailingList) {
                $mailingListSubscriptions = $subscriptionListRep->getUnsyncedByMailinglist($mailingList);

                if ($mailingList->getProvider() == 'mailplus') {
                    $provider = $this->getContainer()->get('opifer.mailplus_provider');
                    $message = $provider->sync($mailingListSubscriptions);

                    $output->writeln($message);
                }
            }
        }
    }
}
