<?php

namespace Opifer\CmsBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as FOSSecurityController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends FOSSecurityController
{
    /**
     * Extends the parent loginAction by checking if the user is already logged in.
     * If so, he will be redirected to the admin dashboard.
     *
     * @param  Request  $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $url = $this->container->get('router')->generate('opifer_cms_dashboard');

            return new RedirectResponse($url, 302);
        }

        return parent::loginAction($request);
    }
}
