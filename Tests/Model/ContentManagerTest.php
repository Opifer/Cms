<?php 

namespace Opifer\ContentBundle\Tests\Model;

use Mockery as m;
use Opifer\ContentBundle\Model\ContentManager;

class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $formFactory;
    private $eavManager;

    public function setUp()
    {
        $this->em = m::mock('Doctrine\ORM\EntityManager');
        $this->formFactory = m::mock('Symfony\Component\Form\FormFactory');
        $this->eavManager = m::mock('Opifer\EavBundle\Manager\EavManager');
    }

    public function testGetClass()
    {
        $content = 'Opifer\ContentBundle\Tests\TestData\Content';
        $template = 'Opifer\ContentBundle\Tests\TestData\Template';
        $manager = new ContentManager($this->em, $this->formFactory, $this->eavManager, $content, $template);

        $this->assertEquals('Opifer\ContentBundle\Tests\TestData\Content', $manager->getClass());
    }
}
