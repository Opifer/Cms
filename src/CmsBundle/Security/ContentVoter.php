<?php

namespace Opifer\CmsBundle\Security;

use Opifer\CmsBundle\Entity\Content;
use Opifer\CmsBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ContentVoter extends Voter
{
    private $security;
    private $container;
    private $roles;

    public function __construct(Security $security, ContainerInterface $container, $roles)
    {
        $this->security = $security;
        $this->container= $container;
        $this->roles = $roles;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // ROLE_SUPER_ADMIN can access all content items
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $subjectRoles = $subject->getRoles();

        if (!$subjectRoles) {
            $subjectRoles = $this->container->getParameter('opifer_cms.default_content_access');
        }

        foreach($user->getRoles() as $role) {
            if (in_array($role, $subjectRoles)) {
                return true;
            }
        }

        return false;
    }

    protected function supports($attribute, $subject)
    {
        return ($subject instanceof Content);
    }
}
