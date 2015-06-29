<?php

namespace Opifer\EavBundle\Tests\Entity;

use Opifer\EavBundle\Entity\FormValue;
use Opifer\EavBundle\Model\Template;

class FormValueTest extends \PHPUnit_Framework_TestCase
{
    public function testTemplate()
    {
        $formValue = new FormValue();
        $template = new Template();

        $expected = $template;
        $formValue->setTemplate($template);
        $actual = $formValue->getTemplate();

        $this->assertSame($expected, $actual);
    }
}