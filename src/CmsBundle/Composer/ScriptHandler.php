<?php

namespace Opifer\CmsBundle\Composer;

use Composer\Script\Event;

class ScriptHandler
{
    public static function installAssets(Event $event)
    {
        $event->getIO()->write('<info>Installing CmsBundle assets</info>');

        $currentDirectory = getcwd();

        $cmdDirectory = __DIR__.'/..';

        chdir($cmdDirectory);

        $command = 'bower install --allow-root';
        exec($command, $output, $status);

        if ($status) {
            throw new \RuntimeException("Running bower install failed with $status\n");
        }

        chdir($currentDirectory);
    }
}
