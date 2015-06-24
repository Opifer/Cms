<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\Menu;
use Opifer\CmsBundle\Entity\Site;

class MenuTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $menu = new Menu();
        $name = 'Some Name';

        $expected = $name;
        $menu->setName($name);
        $actual = $menu->getName();

        $this->assertSame($expected, $actual);
    }

    public function testLft()
    {
        $menu = new Menu();
        $lft = 3;

        $expected = $lft;
        $menu->setLft($lft);
        $actual = $menu->getLft();

        $this->assertSame($expected, $actual);
    }

    public function testLvl()
    {
        $menu = new Menu();
        $lvl = 3;

        $expected = $lvl;
        $menu->setLvl($lvl);
        $actual = $menu->getLvl();

        $this->assertSame($expected, $actual);
    }

    public function testRgt()
    {
        $menu = new Menu();
        $rgt = 3;

        $expected = $rgt;
        $menu->setRgt($rgt);
        $actual = $menu->getRgt();

        $this->assertSame($expected, $actual);
    }

    public function testRoot()
    {
        $menu = new Menu();
        $root = 3;

        $expected = $root;
        $menu->setRoot($root);
        $actual = $menu->getRoot();

        $this->assertSame($expected, $actual);
    }

    public function testParent()
    {
        $menu = new Menu();
        $parent = $menu;

        $expected = $parent;
        $menu->setParent($parent);
        $actual = $menu->getParent();

        $this->assertSame($expected, $actual);
    }

    public function testAddChild()
    {
        $menu = new Menu();
        $children = $menu;

        $expected = $menu;
        $actual = $menu->addChild($children);

        $this->assertSame($expected, $actual);
    }

    public function testHasChildren()
    {
        $menu = new Menu();

        $expected = false;
        $actual = $menu->hasChildren();

        $this->assertSame($expected, $actual);
    }

    public function testSite()
    {
        $menu = new Menu();
        $site = new Site();

        $expected = $site;
        $menu->setSite($site);
        $actual = $menu->getSite();

        $this->assertSame($expected, $actual);
    }

    public function testSort()
    {
        $menu = new Menu();
        $sort = 3;

        $expected = $sort;
        $menu->setSort($sort);
        $actual = $menu->getSort();

        $this->assertSame($expected, $actual);
    }

}