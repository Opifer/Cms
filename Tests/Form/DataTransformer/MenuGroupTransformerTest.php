<?php

namespace Opifer\CmsBundle\Tests\Form\DataTransformer;

use Mockery as m;
use Opifer\CmsBundle\Entity\MenuGroup;
use Opifer\CmsBundle\Form\DataTransformer\MenuGroupTransformer;


class MenuGroupTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransformMenuGroup()
    {
        $key = 2;
        $menuGroup = new MenuGroup();
        $expected = [$key => $menuGroup];
        $id = 2;

        $repository = m::mock('Doctrine\ORM\EntityManager');
        $repository->shouldReceive('findById')->andReturn([$menuGroup]);

        $menuManager = m::mock('Opifer\CmsBundle\Manager\MenuManager');
        $menuManager->shouldReceive('getRepository')->andReturn($repository);

        $menuGroupTransformer = new MenuGroupTransformer($menuManager, $key);

        $actual = $menuGroupTransformer->transform($id);

        $this->assertEquals($expected, $actual);
    }

    public function testReverseTransform()
    {
        $key = 3;
        $id = 3;
        $expected = $id;

        $menuGroup = m::mock('Opifer\CmsBundle\Entity\MenuGroup');
        $menuGroup->shouldReceive('getId')->andReturn($id);

        $menuManager = m::mock('Opifer\CmsBundle\Manager\MenuManager');
        $menuManager->shouldReceive('');

        $menuGroupTransformer = new MenuGroupTransformer($menuManager, $key);

        $this->assertNotEmpty($menuGroup);

        $actual = $menuGroupTransformer->reverseTransform([$key => $menuGroup]);

        $this->assertEquals($actual, $expected);

    }

}