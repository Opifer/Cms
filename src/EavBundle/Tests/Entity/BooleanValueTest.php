<?php

namespace Opifer\EavBundle\Tests\Entity;

use Opifer\EavBundle\Entity\BooleanValue;

class BooleanValueTest extends \PHPUnit_Framework_TestCase
{
    protected $value;

    public function setUp()
    {
        $this->value = new BooleanValue();
    }

    public function testSetTrueValue()
    {
        $this->value->setValue(1);

        $string = (string) $this->value;

        $this->assertSame('true', $string);

        $value = $this->value->getValue();

        $this->assertSame(true, $value);
    }

    public function testSetFalseValue()
    {
        $this->value->setValue(0);

        $string = (string) $this->value;

        $this->assertSame('false', $string);

        $value = $this->value->getValue();

        $this->assertSame(false, $value);
    }

    public function testSetNoValue()
    {
        $string = (string) $this->value;

        $this->assertSame('false', $string);

        $value = $this->value->getValue();

        $this->assertSame(false, $value);
    }
}
