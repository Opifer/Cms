<?php

namespace Opifer\MailingListBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Reservation\ReservationManager;
use Opifer\MailingListBundle\Entity\MailingList;

class SyncMailingListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sync:mailinglist')
            ->setDescription('Sync Mailing List Contacts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contactsManager = $this->getContainer()->get('opifer.contacts_manager');
        $rep = $this->getContainer()->get('doctrine')->getRepository('OpiferMailingListBundle:MailingList');
        
        $synched = $failed = 0;
        $contacts_to_sync = 0;

        $providers = $rep->createQueryBuilder('m')
                            ->select('m.provider')
                            ->where("m.provider IS NOT NULL")
                            ->andWhere("m.status = 'pending' OR m.status = 'failed'")
                            ->groupBy("m.provider")
                            ->getQuery()
                            ->getResult();

        if (!empty($providers)) {
            foreach($providers as $provider) {
                
                $provider_contacts = $rep->createQueryBuilder('m')
                                        ->where("m.provider = :provider")
                                        ->andWhere("m.status = 'pending'")
                                        ->setParameter("provider", $provider['provider'])
                                        ->getQuery()
                                        ->getResult();
                if (!empty($provider_contacts)) {
                    foreach ($provider_contacts as $contact) {
                        $result = $contactsManager->addContactToMailPlus($contact);

                        if ($result) {
                            $contact->setStatus('synched');
                            $synched++;
                        } else {
                            $contact->setStatus('failed');
                            $failed++;
                        }

                        $contactsManager->save($contact);

                        $contacts_to_sync++;
                    }
                }
            }
        }

        $output->writeln('Synched '.$synched.', failed '.$failed.' contacts of '.$contacts_to_sync);
    }
} 