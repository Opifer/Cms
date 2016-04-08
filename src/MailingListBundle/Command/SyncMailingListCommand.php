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
        $rep = $this->getContainer()->get('doctrine')->getRepository('OpiferMailingListBundle:MailingList');
        
        $providers = $rep->createQueryBuilder('m')
                            ->select('m.provider')
                            ->where("m.provider IS NOT NULL")
                            ->groupBy("m.provider")
                            ->getQuery()
                            ->getResult();

        if (!empty($providers)) {
            foreach($providers as $provider) {
                
                $provider_contacts = $rep->createQueryBuilder('m')
                                        ->where("m.provider = :provider")
                                        ->andWhere("m.remoteID IS NULL")
                                        ->setParameter("provider", $provider['provider'])
                                        ->getQuery()
                                        ->getResult();
                //dump($provider_contacts);
            }
        }
        exit;

        $output->writeln();
    }
} 