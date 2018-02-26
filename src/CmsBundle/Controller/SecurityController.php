<?php

namespace Opifer\CmsBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as FOSSecurityController;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends FOSSecurityController
{
    /**
     * @param array $data
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        $secretKey = $this->getParameter('opifer_cms.google_captcha_secret');
        $siteKey = $this->getParameter('opifer_cms.google_captcha_site_key');

        if (!empty($secretKey) && !empty($siteKey)) {
            $data['google_captcha_site_key'] = $siteKey;
        }

        return $this->render('FOSUserBundle:Security:login.html.twig', $data);
    }
}
