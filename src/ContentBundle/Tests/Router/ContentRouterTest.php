<?php

namespace Opifer\ContentBundle\Tests\Router;

use Mockery as m;
use Opifer\ContentBundle\Router\ContentRouter;
use Opifer\ContentBundle\Tests\TestData\Content;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ContentRouterTest extends TestCase
{
    protected $contentManager;
    protected $requestStack;

    public function setUp(): void
    {
        $request = new Request();

        $this->contentManager = m::mock('Opifer\ContentBundle\Model\ContentManager');
        $this->requestStack = m::mock('Symfony\Component\HttpFoundation\RequestStack', [
            'getCurrentRequest' => $request,
        ]);
    }

    public function testMatch()
    {
        $content = new Content();

        $contentRepository = m::mock('Opifer\ContentBundle\Model\ContentRepository');

        $this->contentManager->shouldReceive('getRepository')->andReturn($contentRepository);

        $contentRepository->shouldReceive('findActiveBySlug', 'findActiveByAlias')->andReturn($content);

        $contentRouter = new ContentRouter($this->requestStack, $this->contentManager, null);
        $result = $contentRouter->match('/about');

        $this->assertEquals($content, $result['content']);
    }

    public function testNotMatch()
    {
        $this->expectException(\Symfony\Component\Routing\Exception\ResourceNotFoundException::class);

        $contentRepository = m::mock('Opifer\ContentBundle\Model\ContentRepository');

        $this->contentManager->shouldReceive('getRepository')->andReturn($contentRepository);

        $contentRepository->shouldReceive('findActiveBySlug', 'findActiveByAlias')->andThrow('Doctrine\ORM\NoResultException');

        $contentRouter = new ContentRouter($this->requestStack, $this->contentManager, null);
        $result = $contentRouter->match('/about');
    }

    public function tearDown():void
    {
        m::close();
    }
}
