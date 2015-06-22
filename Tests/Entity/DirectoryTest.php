<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Directory;
use Opifer\CmsBundle\Entity\Site;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $expected = 'Some Name';

        $directory = new Directory();
        $directory->setName($expected);

        $actual = $directory->getName();
        $this->assertSame($expected, $actual);
    }

    public function testSite()
    {
        $expected = new Site();

        $directory = new Directory();
        $directory->setSite($expected);

        $actual = $directory->getSite();
        $this->assertSame($expected, $actual);
    }

    public function testParent()
    {
        $expected = new Directory();
        $expected->setName('parent');

        $directory = new Directory();
        $directory->setName('child');
        $directory->setParent($expected);

        $actual = $directory->getParent();
        $this->assertSame($expected, $actual);
    }
}
