<?php

namespace Opifer\CmsBundle\Security;

use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * ACL Manager
 *
 * Granting access
 * ---------------
 * After entity creation (flush()), add the entity to the grantAccess method;
 * $container->get('opifer.cms.acl_manager')->grantAccess($entity);
 *
 * Optionally, you can pass a user entity, and/or a certain mask level
 * @see http://symfony.com/doc/current/cookbook/security/acl_advanced.html
 *
 * Checking access
 * ---------------
 * An example of checking the access:
 * if (false === $container->get('security.context')->isGranted('EDIT', $entity)) {
 *     throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
 * }
 *
 */
class AclManager
{
    protected $aclProvider;

    protected $securityContext;

    /**
     * Constructor
     *
     * @param MutableAclProvider $aclProvider
     * @param SecurityContext    $securityContext
     */
    public function __construct(MutableAclProvider $aclProvider, SecurityContext $securityContext)
    {
        $this->aclProvider = $aclProvider;
        $this->securityContext = $securityContext;
    }

    /**
     * Granting access
     *
     * @param Object        $domain
     * @param UserInterface $user
     * @param integer       $mask
     *
     * @return boolean
     */
    public function grantAccess($domain, $user = null, $mask = MaskBuilder::MASK_OWNER)
    {
        try {
            $acl = $this->createAcl($domain);
            $acl->insertObjectAce($this->getSecurityEntity($user), $mask);
            $this->aclProvider->updateAcl($acl);

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create ACL
     *
     * @param Object $domain
     *
     * @return \Symfony\Component\Security\Acl\Model\MutableAclInterface
     */
    public function createAcl($domain)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($domain);

        return $this->aclProvider->createAcl($objectIdentity);
    }

    /**
     * get Security Entity
     *
     * @param UserInterface $user
     *
     * @return UserSecurityIdentity
     */
    public function getSecurityEntity($user = null)
    {
        if (is_null($user)) {
            $user = $this->securityContext->getToken()->getUser();
        }

        return UserSecurityIdentity::fromAccount($user);
    }
}
