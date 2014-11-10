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

        $this->contentManager->shouldReceive('findOneBySlug')->andReturn($content);

        $contentRouter = new ContentRouter($this->requestStack, $this->contentManager);
        $result = $contentRouter->match('/about');
        
        $this->assertEquals($content, $result['content']);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testNotMatch()
    {
        $this->contentManager->shouldReceive('findOneBySlug')->andThrow('Doctrine\ORM\NoResultException');

        $contentRouter = new ContentRouter($this->requestStack, $this->contentManager);
        $result = $contentRouter->match('/about');
    }

    public function tearDown()
    {
        m::close();
    }
}
