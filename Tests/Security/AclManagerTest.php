<?php

namespace Opifer\CmsBundle\Tests\Security;

use Mockery as m;
use Opifer\CmsBundle\Security\AclManager;

class AclManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $aclProvider;
    protected $securityContext;
    protected $account;

    public function setUp()
    {
        $this->aclProvider = m::mock('Symfony\Component\Security\Acl\Dbal\MutableAclProvider');
        $this->securityContext = m::mock('Symfony\Component\Security\Core\SecurityContext');
        $this->account = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')
            ->setMockClassName('USI_AccountImpl')
            ->getMock();
        $this->account
            ->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('foo'))
        ;
    }

    public function testGrantAccessToCurrentUser()
    {
        $mutableAclInterface = m::mock('Symfony\Component\Security\Acl\Model\MutableAclInterface');
        $mutableAclInterface->shouldReceive('insertObjectAce')->andReturn(true);
        $this->aclProvider->shouldReceive(array(
            'createAcl' => $mutableAclInterface,
            'updateAcl' => true,
        ));

        $token = m::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->shouldReceive('getUser')->andReturn($this->account);
        $this->securityContext->shouldReceive('getToken')->andReturn($token);

        $domain = new TestEntity();

        $aclManager = new AclManager($this->aclProvider, $this->securityContext);
        $this->assertTrue($aclManager->grantAccess($domain));
    }

    public function testCreateAcl()
    {
        $mutableAclInterface = m::mock('Symfony\Component\Security\Acl\Model\MutableAclInterface');
        $this->aclProvider->shouldReceive('createAcl')->andReturn($mutableAclInterface);

        $entity = new TestEntity();
        $aclManager = new AclManager($this->aclProvider, $this->securityContext);

        $this->assertInstanceOf('Symfony\Component\Security\Acl\Model\MutableAclInterface', $aclManager->createAcl($entity));
    }

    public function testGettingSecurityIdentity()
    {
        $token = m::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->shouldReceive('getUser')->andReturn($this->account);

        $this->securityContext->shouldReceive('getToken')->andReturn($token);

        $aclManager = new AclManager($this->aclProvider, $this->securityContext);
        $aclManager->getSecurityEntity();
    }

    public function tearDown()
    {
        m::close();
    }
}

class TestEntity
{
    public function getObjectIdentifier()
    {
        return 'getObjectIdentifier()';
    }

    public function getId()
    {
        return 'getId()';
    }
}
