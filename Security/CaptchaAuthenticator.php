<?php

namespace Opifer\CmsBundle\Security;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CaptchaAuthenticator implements SimpleFormAuthenticatorInterface
{
    /** @var string */
    protected $secretKey;

    /** @var string */
    protected $siteKey;

    /** @var Request */
    protected $request;

    /** @var UserPasswordEncoderInterface */
    protected $encoder;

    public function __construct($secretKey, $siteKey, RequestStack $requestStack, UserPasswordEncoderInterface $encoder)
    {
        $this->secretKey = $secretKey;
        $this->siteKey = $siteKey;
        $this->request = $requestStack->getCurrentRequest();
        $this->encoder = $encoder;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (empty($this->secretKey) == false) {
            $captcha = $this->request->get('g-recaptcha-response');

            $reCaptcha = new ReCaptcha($this->secretKey);
            $response = $reCaptcha->verify($captcha, $this->request->getClientIp());

            if ($response->isSuccess() == false) {
                throw new AuthenticationException('Captcha not passed');
            }
        }

        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Invalid username or password');
        }

        $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());

        if ($passwordValid) {
            return new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $providerKey,
                $user->getRoles()
            );
        }

        throw new AuthenticationException('Invalid username or password');
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}
