<?php 

namespace Opifer\ContentBundle\Tests\Router;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Opifer\ContentBundle\Router\ContentRouter;
use Opifer\ContentBundle\Tests\TestData\Content;
use Symfony\Component\HttpFoundation\RequestContext;
use Symfony\Component\HttpFoundation\Request;

class ContentRouterTest extends \PHPUnit_Framework_TestCase
{
    protected $contentManager;
    protected $requestStack;

    public function setUp()
    {
        $request = new Request();

        $this->contentManager = m::mock('Opifer\ContentBundle\Model\ContentManager');
        $this->requestStack = m::mock('Symfony\Component\HttpFoundation\RequestStack', [
            'getCurrentRequest' => $request
        ]);
    }

    public function testMatch()
    {
        $content = new Content();

        $repository = m::mock('Opifer\ContentBundle\Model\ContentRepository', [
            'findOneBySlug' => $content
        ]);
        $this->contentManager->shouldReceive('getRepository')->andReturn($repository);

        $contentRouter = new ContentRouter($this->requestStack, $this->contentManager);
        $result = $contentRouter->match('/about');
        
        $this->assertEquals($content, $result['content']);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testNotMatch()
    {
        $repository = m::mock('Opifer\ContentBundle\Model\ContentRepository')
            ->shouldReceive('findOneBySlug')
            ->andThrow('Doctrine\ORM\NoResultException')
            ->mock()
        ;
        $this->contentManager->shouldReceive('getRepository')->andReturn($repository);

        $contentRouter = new ContentRouter($this->requestStack, $this->contentManager);
        $result = $contentRouter->match('/about');
    }

    public function tearDown()
    {
        m::close();
    }
}
