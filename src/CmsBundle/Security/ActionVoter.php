<?php

namespace Opifer\CmsBundle\Security;

use Opifer\CmsBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ActionVoter extends Voter
{
    private $security;
    private $container;

    public function __construct(Security $security, ContainerInterface $container)
    {
        $this->security = $security;
        $this->container= $container;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        foreach($user->getRoles() as $role){
            $permissions = $this->container->getParameter('opifer_cms.permissions');
            if (in_array($attribute, $permissions[$role])) {
                return true;
            }
        }

        return false;
    }

    protected function supports($attribute, $subject)
    {
        $permissionRegistry = $this->container->get('opifer.cms.permission_registry');

        return $permissionRegistry->hasPermission($attribute);
    }
}
