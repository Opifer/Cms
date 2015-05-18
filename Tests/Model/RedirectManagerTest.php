<?php

namespace Opifer\RedirectBundle\Tests\Model;

use Mockery as m;
use Opifer\RedirectBundle\Model\RedirectManager;
use Opifer\RedirectBundle\Tests\TestData\Redirect;

class RedirectManagerTest extends \PHPUnit_Framework_TestCase
{
    private $om;
    private $redirectClass = 'Opifer\RedirectBundle\Tests\TestData\Redirect';

    public function setUp()
    {
        $this->om = m::mock('Doctrine\ORM\EntityManager');
    }

    public function testGetClass()
    {
        $manager = new RedirectManager($this->om, $this->redirectClass);

        $this->assertEquals('Opifer\RedirectBundle\Tests\TestData\Redirect', $manager->getClass());
    }

    public function testCreateNew()
    {
        $manager = new RedirectManager($this->om, $this->redirectClass);

        $this->assertEquals(new Redirect(), $manager->createNew());
    }

    public function testSaveNew()
    {
        $this->om->shouldReceive('persist');
        $this->om->shouldReceive('flush');

        $manager = new RedirectManager($this->om, $this->redirectClass);

        $actual = $manager->save($manager->createNew());

        $this->assertInstanceOf('Opifer\RedirectBundle\Model\Redirect', $actual);
    }

    public function testSaveExisting()
    {
        $this->om->shouldNotReceive('persist');
        $this->om->shouldReceive('flush');

        $manager = new RedirectManager($this->om, $this->redirectClass);

        $object = $manager->createNew();
        $object->setId(12);

        $actual = $manager->save($object);

        $this->assertInstanceOf('Opifer\RedirectBundle\Model\Redirect', $actual);
    }

    public function testRemove()
    {
        $this->om->shouldReceive('remove');
        $this->om->shouldReceive('flush');

        $manager = new RedirectManager($this->om, $this->redirectClass);

        $manager->remove($manager->createNew());
    }

    public function tearDown()
    {
        m::close();
    }
}
