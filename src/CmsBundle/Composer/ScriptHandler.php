<?php

namespace Opifer\CmsBundle\Composer;

use Composer\Script\Event;

class ScriptHandler
{
    public static function installAssets(Event $event)
    {
        $event->getIO()->write('<info>Installing CmsBundle assets</info>');

        $currentDirectory = getcwd();

        $cmdDirectory = $currentDirectory.'/vendor/opifer/cms-bundle';

        chdir($cmdDirectory);

        $command = 'bower install';
        $command .= (getenv('SYMFONY_ENV') == 'prod') ? ' --allow-root' : '';
        exec($command, $output, $status);

        if ($status) {
            throw new \RuntimeException("Running bower install failed with $status\n");
        }

        chdir($currentDirectory);
    }
}
