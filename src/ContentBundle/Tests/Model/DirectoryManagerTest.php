<?php 

namespace Opifer\ContentBundle\Tests\Model;

use Mockery as m;
use Opifer\ContentBundle\Model\DirectoryManager;

class DirectoryManagerTest extends \PHPUnit_Framework_TestCase
{
    private $em;

    public function setUp()
    {
        $this->em = m::mock('Doctrine\ORM\EntityManager');
    }

    public function testGetClass()
    {
        $manager = new DirectoryManager($this->em, 'Opifer\ContentBundle\Tests\TestData\Directory');

        $this->assertEquals('Opifer\ContentBundle\Tests\TestData\Directory', $manager->getClass());
    }

    public function testCreate()
    {
        $manager = new DirectoryManager($this->em, 'Opifer\ContentBundle\Tests\TestData\Directory');

        $this->assertInstanceOf('Opifer\ContentBundle\Tests\TestData\Directory', $manager->create());
    }

    public function tearDown()
    {
        m::close();
    }
}
