<?php

namespace Opifer\EavBundle\Tests\Model;

use Opifer\EavBundle\Model\Schema;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

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

    public function testName()
    {
        $schema = new Schema();
        $name = 'Some Name';

        $expected = $name;
        $schema->setName($name);
        $actual = $schema->getName();

        $this->assertSame($expected, $actual);
    }

    public function testDisplayName()
    {
        $schema = new Schema();
        $displayName = 'Some Displayname';

        $expected = $displayName;
        $schema->setDisplayName($displayName);
        $actual = $schema->getDisplayName();

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