<?php 

namespace Opifer\ContentBundle\Tests\Model;

use Mockery as m;
use Opifer\ContentBundle\Model\LayoutManager;

class LayoutManagerTest extends \PHPUnit_Framework_TestCase
{
    private $em;

    public function setUp()
    {
        $this->em = m::mock('Doctrine\ORM\EntityManager');
    }

    public function testGetClass()
    {
        $manager = new LayoutManager($this->em, 'Opifer\ContentBundle\Tests\TestData\Layout');

        $this->assertEquals('Opifer\ContentBundle\Tests\TestData\Layout', $manager->getClass());
    }

    public function testCreate()
    {
        $manager = new LayoutManager($this->em, 'Opifer\ContentBundle\Tests\TestData\Layout');

        $this->assertInstanceOf('Opifer\ContentBundle\Tests\TestData\Layout', $manager->create());
    }
}
