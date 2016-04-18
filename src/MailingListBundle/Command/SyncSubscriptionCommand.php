<?php

namespace Opifer\MailingListBundle\Command;

use Opifer\MailingListBundle\Provider\MailPlusProvider;
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
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subscriptionRepository = $this->getDoctrine()->getRepository('OpiferMailingListBundle:Subscription');
        $mailingLists = $this->getDoctrine()->getRepository('OpiferMailingListBundle:MailingList')->findAll();

        if (!empty($mailingLists)) {
            /** @var MailingList $mailingList */
            foreach ($mailingLists as $mailingList) {
                if ($mailingList->getProvider() == 'mailplus') {
                    $output->writeln(sprintf('Synchronizing subscriptions for mailinglist %s', $mailingList->getDisplayName()));

                    /** @var MailPlusProvider $provider */
                    $provider = $this->getContainer()->get('opifer.mailplus_provider');

                    $synced = $failed = 0;

                    $subscriptions = $subscriptionRepository->getUnsyncedByMailinglist($mailingList);

                    /** @var Subscription $subscription */
                    foreach ($subscriptions as $subscription) {
                        $success = $provider->sync($subscription);

                        if ($success) {
                            $synced++;
                        } else {
                            $failed++;
                        }
                    }

                    $output->writeln(sprintf('%d synched and %d failed of %d subscriptions', $synced, $failed, count($subscriptions)));
                }
            }
        }
    }

    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }
}
