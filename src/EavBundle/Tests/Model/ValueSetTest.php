<?php

namespace Opifer\EavBundle\Tests\Model;

use Mockery as m;
use Opifer\EavBundle\Model\Schema;
use Opifer\EavBundle\Model\ValueSet;
use PHPUnit\Framework\TestCase;

class ValueSetTest extends TestCase
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
