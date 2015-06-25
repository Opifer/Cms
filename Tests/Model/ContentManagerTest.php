<?php 

namespace Opifer\ContentBundle\Tests\Model;

use Mockery as m;
use Opifer\ContentBundle\Model\ContentManager;

class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $formFactory;
    private $eavManager;
    private $tokenStorage;

    private $contentClass = 'Opifer\ContentBundle\Tests\TestData\Content';
    private $templateClass = 'Opifer\ContentBundle\Tests\TestData\Template';

    public function setUp()
    {
        $this->em = m::mock('Doctrine\ORM\EntityManager');
        $this->formFactory = m::mock('Symfony\Component\Form\FormFactory');
        $this->eavManager = m::mock('Opifer\EavBundle\Manager\EavManager');
        $this->tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
    }

    public function testGetClass()
    {
        $manager = new ContentManager($this->em, $this->formFactory, $this->eavManager, $this->contentClass, $this->templateClass, $this->tokenStorage);

        $this->assertEquals('Opifer\ContentBundle\Tests\TestData\Content', $manager->getClass());
    }

    public function tearDown()
    {
        m::close();
    }
}
