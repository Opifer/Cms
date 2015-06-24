<?php

namespace Opifer\CmsBundle\Tests\Entity;

use Opifer\CmsBundle\Entity\MenuItem;
use Opifer\CmsBundle\Entity\Content;

class MenuItemTest extends \PHPUnit_Framework_TestCase
{
    public function testLink()
    {
        $menuItem = new MenuItem();
        $link = 'Some Link';

        $expected = $link;
        $menuItem->setLink($link);
        $actual = $menuItem->getLink();

        $this->assertSame($expected, $actual);
    }

    public function testContent()
    {
        $menuItem = new MenuItem();
        $content = new Content();

        $expected = $content;
        $menuItem->setContent($content);
        $actual = $menuItem->getContent();

        $this->assertSame($expected, $actual);
    }

    public function testHiddenMobile()
    {
        $menuItem = new MenuItem();
        $hiddenMobile = false;

        $expected = $hiddenMobile;
        $menuItem->setHiddenMobile($hiddenMobile);
        $actual = $menuItem->isHiddenMobile();

        $this->assertSame($expected, $actual);
    }

    public function testHiddenTabletPortrait()
    {
        $menuItem = new MenuItem();
        $hiddenTabletPortrait = true;

        $expected = $hiddenTabletPortrait;
        $menuItem->setHiddenTabletPortrait($hiddenTabletPortrait);
        $actual = $menuItem->isHiddenTabletPortrait();

        $this->assertSame($expected, $actual);
    }

    public function testHiddenTabletLandscape()
    {
        $menuItem = new MenuItem();
        $hiddenTabletLandscape = true;

        $expected = $hiddenTabletLandscape;
        $menuItem->setHiddenTabletLandscape($hiddenTabletLandscape);
        $actual = $menuItem->isHiddenTabletLandscape();

        $this->assertSame($expected, $actual);
    }

    public function testHiddenDesktop()
    {
        $menuItem = new MenuItem();
        $hiddenDesktop = false;

        $expected = $hiddenDesktop;
        $menuItem->setHiddenDesktop($hiddenDesktop);
        $actual = $menuItem->isHiddenDesktop();

        $this->assertSame($expected, $actual);
    }

    public function testParameters()
    {
        $menuItem = new MenuItem();
        $parameters = array();

        $expected = $parameters;
        $menuItem->setParameters($parameters);
        $actual = $menuItem->getParameters();

        $this->assertSame($expected, $actual);
    }

}