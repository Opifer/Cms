<?php

namespace Opifer\CmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Opifer\CmsBundle\Entity\Cron;

/**
 * This command is the main entry point for pending cronjobs
 *
 * To add cronjobs, simply create your own symfony command and add that command
 * to the cronjobs page in the cms.
 *
 * @see  http://symfony.com/doc/current/cookbook/console/console_command.html
 */
class CronRunCommand extends ContainerAwareCommand
{
    /** @var string */
    private $env;

    /** @var ManagerRegistry */
    private $registry;

    /** @var OutputInterface */
    private $output;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('opifer:cron:run')
            ->setDescription('Runs all due cronjobs from the queue.');
    }

    /**
     * Execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->env = $input->getOption('env');
        $this->registry = $this->getContainer()->get('doctrine');
        $this->output = $output;

        $this->runCrons();
    }

    /**
     * Run jobs
     *
     * @return void
     */
    private function runCrons()
    {
        $due = $this->getRepository()->findDue();

        foreach ($due as $cron) {
            $this->startCron($cron);
        }

        $this->output->writeln('All done.');
    }

    /**
     * Start a cron
     *
     * @param Cron $cron
     *
     * @return void
     */
    private function startCron(Cron $cron)
    {
        $this->output->writeln(sprintf('Started %s.', $cron));
        $this->changeState($cron, Cron::STATE_RUNNING);

        $pb = $this->getCommandProcessBuilder();
        $pb->add($cron->getCommand());

        $process = $pb->getProcess();
        $process->run(function ($type, $buffer) { // or ->start() to make processes run asynchronously
            if (Process::ERR === $type) {
                $this->output->writeln('ERR > '.$buffer);
            } else {
                $this->output->writeln(' > '.$buffer);
            }
        });

        if (!$process->isSuccessful()) {
            $this->changeState($cron, Cron::STATE_FAILED);
        }

        $this->changeState($cron, Cron::STATE_FINISHED);
    }

    /**
     * Change the state of the cron
     *
     * @param Cron   $cron
     * @param string $state
     *
     * @return void
     */
    private function changeState(Cron $cron, $state)
    {
        $cron->setState($state);

        $em = $this->getEntityManager();
        $em->persist($cron);
        $em->flush($cron);
    }

    /**
     * Get the process builder
     *
     * @return \Symfony\Component\Process\ProcessBuilder
     */
    private function getCommandProcessBuilder()
    {
        $pb = new ProcessBuilder();

        // PHP wraps the process in "sh -c" by default, but we need to control
        // the process directly.
        if (! defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $pb->add('exec');
        }

        $pb
            ->add('php')
            ->add($this->getContainer()->getParameter('kernel.root_dir').'/console')
            ->add('--env='.$this->env)
        ;

        return $pb;
    }

    /**
     * Get the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->registry->getManagerForClass('OpiferCmsBundle:Cron');
    }

    /**
     * Get repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('OpiferCmsBundle:Cron');
    }
}
