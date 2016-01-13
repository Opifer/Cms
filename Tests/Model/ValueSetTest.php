<?php

namespace Opifer\EavBundle\Tests\Model;

use Opifer\EavBundle\Model\Schema;
use Mockery as m;
use Opifer\EavBundle\Model\ValueSet;

class ValueSetTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $valueSet = new ValueSet();
        $schema = new Schema();

        $expected = $schema;
        $valueSet->setSchema($schema);
        $actual = $valueSet->getSchema();

        $this->assertSame($expected, $actual);
    }
}