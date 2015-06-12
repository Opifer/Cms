<?php

namespace Opifer\CmsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;

class CommandController extends Controller
{
    /**
     * Clears the cache for the current environment
     *
     * @Route("/cache/clear", name="opifer.cms.cache_clear")
     *
     * @throws RuntimeException when the command was not run succesfully
     *
     * @return RedirectResponse returns the user to the page it came from
     */
    public function clearCacheAction()
    {
        $process = new Process('php '.$this->get('kernel')->getRootDir().
            '/console cache:clear --no-debug --env='.
            $this->get('kernel')->getEnvironment());

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
}
