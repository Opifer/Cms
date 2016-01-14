<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Schema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testPostNotify()
    {
        $schema = new Schema();
        $postNotify = 'Some Post Notify';

        $expected = $postNotify;
        $schema->setPostNotify($postNotify);
        $actual = $schema->getPostNotify();

        $this->assertSame($expected, $actual);
    }
}