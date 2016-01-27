<?php

namespace Opifer\CmsBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /** @var RouterInterface */
    protected $router;

    /**
     * Constructor.
     *
     * @param HttpUtils       $httpUtils
     * @param array           $options   Options for processing a successful authentication attempt.
     * @param RouterInterface $router
     */
    public function __construct(HttpUtils $httpUtils, array $options = array(), RouterInterface $router)
    {
        parent::__construct($httpUtils, $options);

        $this->router = $router;
    }

    /**
     * Checks if the user has actually filled in some mandatory data. If not, it redirects to the users'
     * profile page.
     *
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($user = $token->getUser()) {
            if (!$user->getFirstName() || !$user->getLastName()) {
                return new RedirectResponse($this->router->generate('opifer_cms_user_profile'));
            }
        }

        return parent::onAuthenticationSuccess($request, $token);
    }
}
