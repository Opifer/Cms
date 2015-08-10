<?php

namespace Opifer\EavBundle\Tests\Entity;

use Opifer\EavBundle\Entity\FormValue;
use Opifer\EavBundle\Model\Schema;

class FormValueTest extends \PHPUnit_Framework_TestCase
{
    public function testSchema()
    {
        $formValue = new FormValue();
        $schema = new Schema();

        $expected = $schema;
        $formValue->setSchema($schema);
        $actual = $formValue->getSchema();

        $this->assertSame($expected, $actual);
    }
}