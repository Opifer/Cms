<?php

namespace Opifer\CmsBundle\Tests\Form\DataTransformer;

use Monolog\Test\TestCase;
use Opifer\CmsBundle\Form\DataTransformer\SlugTransformer;

class SlugTransformerTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new SlugTransformer();

        $this->assertEquals('test', $transformer->transform('directory/test'));
    }

    public function testTransformIndex()
    {
        $transformer = new SlugTransformer();

        $this->assertEquals(null, $transformer->transform(null));

        $this->assertEquals('/', $transformer->transform('directory/'));
        $this->assertEquals('/', $transformer->transform('/'));
    }

    public function testReverseTransform()
    {
        $transformer = new SlugTransformer();

        $this->assertEquals('test', $transformer->reverseTransform('test'));
    }
}
