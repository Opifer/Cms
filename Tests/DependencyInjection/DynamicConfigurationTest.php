<?php

namespace Opifer\CmsBundle\Tests\DependencyInjection;

use Mockery as m;
use Opifer\CmsBundle\DependencyInjection\DynamicConfiguration;
use Opifer\CmsBundle\Entity\Setting;

class DynamicConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $em;

    public function setUp()
    {
        $this->em = m::mock('Doctrine\ORM\EntityManager');
    }

    public function testMagicGet()
    {
        $expected = 'SomeValue';

        $setting = new Setting();
        $setting->setName('key');
        $setting->setValue('SomeValue');

        $repository = m::mock('Doctrine\ORM\EntityRepository');
        $repository->shouldReceive('findAll')->andReturn([$setting]);

        $this->em->shouldReceive('getRepository')->with('OpiferCmsBundle:Setting')
            ->andReturn($repository);

        $config = new DynamicConfiguration($this->em);
        $actual = $config->__get('key');

        $this->assertEquals($expected, $actual);
    }

    public function testMagicIsset()
    {
        $setting = new Setting();
        $setting->setName('key');
        $setting->setValue('SomeValue');

        $repository = m::mock('Doctrine\ORM\EntityRepository');
        $repository->shouldReceive('findAll')->andReturn([$setting]);

        $this->em->shouldReceive('getRepository')->with('OpiferCmsBundle:Setting')
            ->andReturn($repository);

        $config = new DynamicConfiguration($this->em);
        $actual = $config->__isset('key');

        $this->assertTrue($actual);
    }

    public function tearDown()
    {
        m::close();
    }
}
