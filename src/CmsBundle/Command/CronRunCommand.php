<?php

namespace Opifer\CmsBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Opifer\CmsBundle\Entity\Cron;
use Opifer\CmsBundle\Repository\CronRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * This command is the main entry point for pending cronjobs.
 *
 * To add cronjobs, create your own symfony command and add that command
 * to the cronjobs page in the cms.
 *
 * @see  http://symfony.com/doc/current/cookbook/console/console_command.html
 */
class CronRunCommand extends ContainerAwareCommand
{
    /** @var string */
    protected $env;

    /** @var ManagerRegistry */
    protected $registry;

    /** @var OutputInterface */
    protected $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('opifer:cron:run')
            ->setDescription('Runs all due cronjobs from the queue.');
    }

    /**
     * Execute.
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
     * Run jobs.
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
     * Checks if the cronjob is currently locked.
     *
     * This can be overridden with any other lock check. E.g. usage of Redis in case of a load balanced environment
     *
     * @param Cron $cron
     *
     * @return bool
     */
    protected function isLocked(Cron $cron)
    {
        $hourAgo = new \DateTime('-65 minutes');
        if ($cron->getState() === Cron::STATE_RUNNING && $cron->getStartedAt() > $hourAgo) {
            return true;
        }

        return false;
    }

    /**
     * Start a cron.
     *
     * @param Cron $cron
     */
    private function startCron(Cron $cron)
    {
        if ($this->isLocked($cron)) {
            return;
        }

        $this->output->writeln(sprintf('Started %s.', $cron));
        $this->changeState($cron, Cron::STATE_RUNNING);

        $pb = $this->getCommandProcessBuilder();
        $parts = explode(' ', $cron->getCommand());
        foreach ($parts as $part) {
            $pb->add($part);
        }

        $process = $pb->getProcess();
        $process->setTimeout(3600);

        try {
            $process->mustRun();

            $this->output->writeln(' > '.$process->getOutput());

            if (!$process->isSuccessful()) {
                $this->output->writeln(' > '.$process->getErrorOutput());
                if(strpos($process->getErrorOutput(), 'timeout') !== false) {
                    $this->changeState($cron, Cron::STATE_TERMINATED, $process->getErrorOutput());
                } else {
                    $this->changeState($cron, Cron::STATE_FAILED, $process->getErrorOutput());
                }
            } else {
                $this->changeState($cron, Cron::STATE_FINISHED);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if(strpos($e->getMessage(), 'timeout') !== false) {
                $this->output->writeln(' > '.$e->getMessage());
                $this->changeState($cron, Cron::STATE_TERMINATED, $e->getMessage());
            } else {
                $this->output->writeln(' > '.$e->getMessage());
                $this->changeState($cron, Cron::STATE_FAILED, $e->getMessage());
            }
        }
    }

    /**
     * Change the state of the cron.
     *
     * @param Cron   $cron
     * @param string $state
     */
    private function changeState(Cron $cron, $state, $lastError = null)
    {
        $cron->setState($state);
        $cron->setLastError($lastError);

        $em = $this->getEntityManager();
        $em->persist($cron);
        $em->flush($cron);
    }

    /**
     * Get the process builder.
     *
     * @return \Symfony\Component\Process\ProcessBuilder
     */
    private function getCommandProcessBuilder()
    {
        $pb = new ProcessBuilder();

        // PHP wraps the process in "sh -c" by default, but we need to control
        // the process directly.
        if (!defined('PHP_WINDOWS_VERSION_MAJOR')) {
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
     * Get the entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->registry->getManagerForClass('OpiferCmsBundle:Cron');
    }

    /**
     * Get repository.
     *
     * @return CronRepository
     */
    private function getRepository()
    {
        return $this->getEntityManager()->getRepository('OpiferCmsBundle:Cron');
    }
}
