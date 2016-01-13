<?php

namespace Opifer\EavBundle\Tests\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Opifer\EavBundle\Model\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectClass()
    {
        $template = new Template();
        $objectClass= 'Some ObjectClass';

        $expected = $objectClass;
        $template->setObjectClass($objectClass);
        $actual = $template->getObjectClass();

        $this->assertSame($expected, $actual);
    }

    public function testName()
    {
        $template = new Template();
        $name = 'Some Name';

        $expected = $name;
        $template->setName($name);
        $actual = $template->getName();

        $this->assertSame($expected, $actual);
    }

    public function testDisplayName()
    {
        $template = new Template();
        $displayName = 'Some Displayname';

        $expected = $displayName;
        $template->setDisplayName($displayName);
        $actual = $template->getDisplayName();

        $this->assertSame($expected, $actual);
    }

    public function testPresentation()
    {
        $template = new Template();
        $presentation = 'presentation';

        $expected = $presentation;
        $template->setPresentation($presentation);
        $actual = $template->getPresentation();

        $this->assertSame($expected, $actual);
    }

    public function testAllowedInAttributes()
    {
        $template = new Template();
        $arrayCollection = new ArrayCollection();

        $expected = $arrayCollection;
        $template->setAllowedInAttributes($arrayCollection);
        $actual = $template->getAllowedInAttributes();

        $this->assertSame($expected, $actual);
    }
}