<?php

namespace Opifer\CmsBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

class ExceptionListener extends BaseExceptionListener
{
    protected function setTargetPath(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return;
        }
        
        parent::setTargetPath($request);
    }
}