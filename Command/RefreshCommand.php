<?php

namespace Opifer\CmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Refresh Command
 *
 * A simple way to refresh the application in one command
 *
 * Usage from inside root:
 * app/console opifer:refresh
 *
 * To change the environment (default is dev):
 * app/console opifer:refresh --env=prod
 *
 * @see  http://symfony.com/doc/current/cookbook/console/console_command.html
 * @see  http://symfony.com/doc/current/components/console/introduction.html
 */
class RefreshCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('opifer:refresh')
            ->setDescription('Refreshes the app')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearCache($input, $output);
        $this->installAssets($input, $output);
        $this->asseticDump($input, $output);
    }

    /**
     * Clears the cache for the current environment
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function clearCache($input, $output)
    {
        $command = $this->getApplication()->find('cache:clear');

        $newInput = new ArrayInput([
            'command' => 'cache:clear',
            '--env'   => $input->getOption('env'),
        ]);

        $command->run($newInput, $output);
    }

    /**
     * Installs all assets as symlinks
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function installAssets($input, $output)
    {
        $command = $this->getApplication()->find('assets:install');

        $newInput = new ArrayInput([
            'command'    => 'assets:install',
            'target'     => 'web',
            '--symlink'  => ($input->getOption('env') == 'dev') ? true : false,
            '--relative' => true,
        ]);

        $command->run($newInput, $output);
    }

    /**
     * Dump the assets to the web directory
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    private function asseticDump($input, $output)
    {
        $command = $this->getApplication()->find('assetic:dump');

        $newInput = new ArrayInput([
            'command' => 'assetic:dump',
        ]);

        $command->run($newInput, $output);
    }
}
