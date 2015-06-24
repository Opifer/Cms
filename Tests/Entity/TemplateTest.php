<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testPostNotify()
    {
        $template = new Template();
        $postNotify = 'Some Post Notify';

        $expected = $postNotify;
        $template->setPostNotify($postNotify);
        $actual = $template->getPostNotify();

        $this->assertSame($expected, $actual);
    }
}