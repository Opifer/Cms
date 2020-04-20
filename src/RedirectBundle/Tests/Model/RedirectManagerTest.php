<?php

namespace Opifer\RedirectBundle\Tests\Model;

use Doctrine\ORM\EntityManager;
use Mockery as m;
use Opifer\RedirectBundle\Model\RedirectManager;
use Opifer\RedirectBundle\Tests\TestData\Redirect;
use PHPUnit\Framework\TestCase;

class RedirectManagerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private $om;

    public function setUp(): void
    {
        $this->om = m::mock(EntityManager::class);
    }

    public function testGetClass()
    {
        $manager = new RedirectManager($this->om, Redirect::class);

        $this->assertEquals(Redirect::class, $manager->getClass());
    }

    public function testCreateNew()
    {
        $manager = new RedirectManager($this->om, Redirect::class);

        $this->assertEquals(new Redirect(), $manager->createNew());
    }

    public function testSaveNew()
    {
        $this->om->shouldReceive('persist');
        $this->om->shouldReceive('flush');

        $manager = new RedirectManager($this->om, Redirect::class);

        $actual = $manager->save($manager->createNew());

        $this->assertInstanceOf(Redirect::class, $actual);
    }

    public function testSaveExisting()
    {
        $this->om->shouldNotReceive('persist');
        $this->om->shouldReceive('flush');

        $manager = new RedirectManager($this->om, Redirect::class);

        $object = $manager->createNew();
        $object->setId(12);

        $actual = $manager->save($object);

        $this->assertInstanceOf(Redirect::class, $actual);
    }

    public function testRemove()
    {
        $this->om->shouldReceive('remove');
        $this->om->shouldReceive('flush');

        $manager = new RedirectManager($this->om, Redirect::class);

        $manager->remove($manager->createNew());
    }

    public function tearDown(): void
    {
        m::close();
    }
}
