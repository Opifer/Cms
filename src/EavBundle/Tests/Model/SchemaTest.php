<?php

namespace Opifer\EavBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Opifer\EavBundle\Model\Schema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectClass()
    {
        $schema = new Schema();
        $objectClass= 'Some ObjectClass';

        $expected = $objectClass;
        $schema->setObjectClass($objectClass);
        $actual = $schema->getObjectClass();

        $this->assertSame($expected, $actual);
    }

    public function testAllowedInAttributes()
    {
        $schema = new Schema();
        $arrayCollection = new ArrayCollection();

        $expected = $arrayCollection;
        $schema->setAllowedInAttributes($arrayCollection);
        $actual = $schema->getAllowedInAttributes();

        $this->assertSame($expected, $actual);
    }
}