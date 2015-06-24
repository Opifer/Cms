<?php

namespace Opifer\CmsBundle\Tests\Entity;

use bar\baz\source_with_namespace;
use Opifer\CmsBundle\Entity\Site;

class SiteTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $site = new Site();
        $name = 'Some Name';

        $expected = $name;
        $site->setName($name);
        $actual = $site->getName();

        $this->assertSame($expected, $actual);
    }

    public function testDescription()
    {
        $site = new Site();
        $description = 'Some Description';

        $expected = $description;
        $site->setDescription($description);
        $actual = $site->getDescription();

        $this->assertSame($expected,$actual);
    }

    public function testDomain()
    {
        $site = new Site();
        $domain = 'Some Domain';

        $expected = $domain;
        $site->setDomain($domain);
        $actual = $site->getDomain();

        $this->assertSame($expected, $actual);
    }

    public function testCookieDomain()
    {
        $site = new Site();
        $cookieDomain = 'Some Cookie';

        $expected = $cookieDomain;
        $site->setCookieDomain($cookieDomain);
        $actual = $site->getCookieDomain();

        $this->assertSame($expected, $actual);
    }

    public function testDefaultLocale()
    {
        $site = new Site();
        $default = 'Some Default Locale';

        $expected = $default;
        $site->setDefaultLocale($default);
        $actual = $site->getDefaultLocale();

        $this->assertSame($expected, $actual);
    }

}