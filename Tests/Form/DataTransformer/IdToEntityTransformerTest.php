<?php

namespace Opifer\ContentBundle\Tests\Model;

use Mockery as m;
use Opifer\ContentBundle\Form\DataTransformer\IdToEntityTransformer;
use Opifer\ContentBundle\Tests\TestData\Entity;

class IdToEntityTransformerTest extends \PHPUnit_Framework_TestCase
{
    private $contentManager;
    private $repository;
    private $entity;

    public function setUp()
    {
        $this->contentManager = m::mock('Opifer\ContentBundle\Model\ContentManagerInterface');
        $this->repository = m::mock('Doctrine\ORM\EntityRepository');
        $this->entity = new Entity();
        $this->entity->setId(1234);
    }

    public function testTransform()
    {
        $transformer = new IdToEntityTransformer($this->contentManager);
        $result = $transformer->transform(null);

        $this->assertNull($result);

        $result = $transformer->transform($this->entity);

        $this->assertEquals(1234, $result);
    }

    public function testReverseTransform()
    {
        $transformer = new IdToEntityTransformer($this->contentManager);
        $result = $transformer->reverseTransform(null);

        $this->assertNull($result);

        $this->contentManager->shouldReceive('getRepository')->andReturn($this->repository);
        $this->repository->shouldReceive('find')->andReturn($this->entity);

        $transformer = new IdToEntityTransformer($this->contentManager);
        $result = $transformer->reverseTransform(1234);

        $this->assertEquals($this->entity, $result);
    }

    public function tearDown()
    {
        m::close();
    }
}
