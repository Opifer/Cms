<?php

namespace Opifer\CmsBundle\Command;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Symfony\Component\Console\Input\InputOption;

/**
 * Class SchemaUpdateCommand
 *
 * Updates all schemas by running fixtures in the DataFixtures\ORM\Schemas namespace
 *
 * @package Opifer\CmsBundle\Command
 */
class SchemaUpdateCommand extends ContainerAwareCommand
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('opifer:schema:update')
            ->setDescription('Run data fixtures for schemas and attributes')
            ->addOption(
                'fixtures',
                null,
                InputOption::VALUE_OPTIONAL,
                'Fixtures path',
                'src/AppBundle/DataFixtures/ORM/Schemas/'
            );

    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();

        $output->writeln("<comment>Updating...</comment>\n");

        $em = $this->container->get('doctrine.orm.entity_manager');

        $loader = new ContainerAwareLoader($this->container);

        $loader->loadFromDirectory($input->getOption('fixtures'));

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);

        $executor->setLogger(function($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });

        $executor->execute($loader->getFixtures(), true);
    }
}