<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Setting;

class SettingTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $setting = new Setting();
        $id = 3;

        $expected = $id;
        $setting->setId($id);
        $actual = $setting->getId();

        $this->assertSame($expected, $actual);
    }

    public function testExtension()
    {
        $setting = new Setting();
        $extension = 'Some Extension';

        $expected = $extension;
        $setting->setExtension($extension);
        $actual = $setting->getExtension();

        $this->assertSame($expected, $actual);
    }

    public function testValue()
    {
        $setting = new Setting();
        $value = 'Some Value';

        $expected = $value;
        $setting->setValue($value);
        $actual = $setting->getValue();

        $this->assertSame($expected, $actual);
    }

    public function testDescription()
    {
        $setting = new Setting();
        $description = 'Some Description';

        $expected = $description;
        $setting->setDescription($description);
        $actual = $setting->getDescription();

        $this->assertSame($expected, $actual);
    }

    public function testName()
    {
        $setting = new Setting();
        $name = 'Some Name';

        $expected = $name;
        $setting->setName($name);
        $actual = $setting->getName();

        $this->assertSame($expected, $actual);
    }

    public function testType()
    {
        $setting = new Setting();
        $type = 'Some Type';

        $expected = $type;
        $setting->setType($type);
        $actual = $setting->getType();

        $this->assertSame($expected, $actual);
    }

    public function testMin()
    {
        $setting = new Setting();
        $min = 'Some Minimal';

        $expected = $min;
        $setting->setMin($min);
        $actual = $setting->getMin();

        $this->assertSame($expected, $actual);
    }

    public function testMax()
    {
        $setting = new Setting();
        $max = 'Some Maximum';

        $expected = $max;
        $setting->setMax($max);
        $actual = $setting->getMax();

        $this->assertSame($expected, $actual);
    }

    public function testChoices()
    {
        $setting = new Setting();
        $choices = 'Some Choices';

        $expected = $choices;
        $setting->setChoices($choices);
        $actual = $setting->getChoices();

        $this->assertSame($expected, $actual);
    }
}