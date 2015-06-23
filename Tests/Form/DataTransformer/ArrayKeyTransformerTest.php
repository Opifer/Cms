<?php

namespace Opifer\ContentBundle\Tests\Model;

use Opifer\ContentBundle\Form\DataTransformer\ArrayKeyTransformer;

class ArrayKeyTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $transformer = new ArrayKeyTransformer('key');
        $result = $transformer->transform('value');

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('value', $result['key']);
    }

    public function testReverseTransform()
    {
        $transformer = new ArrayKeyTransformer('key');
        $result = $transformer->reverseTransform(['key' => 'value']);

        $this->assertEquals('value', $result);
    }
}
