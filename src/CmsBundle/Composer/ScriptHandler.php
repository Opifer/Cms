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

        exec('bower update --allow-root', $output, $return_var);
        $event->getIO()->write('<info>'.implode("\n", $output).'</info>');
        if ($return_var) {
            throw new \RuntimeException("Running bower install failed with $return_var\n");
        }

        exec('gulp default', $output, $return_var);
        $event->getIO()->write('<info>'.implode("\n", $output).'</info>');
        if ($return_var) {
            throw new \RuntimeException("Running gulp failed with $return_var\n");
        }

        chdir($currentDirectory);
    }
}
