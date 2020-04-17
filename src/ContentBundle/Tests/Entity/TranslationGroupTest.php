<?php

namespace Opifer\ContentBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CmsBundle\Entity\Locale;
use Opifer\ContentBundle\Entity\TranslationGroup;
use Opifer\CmsBundle\Entity\Content;

class TranslationGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRouteMapping()
    {
        $locale1 = new Locale();
        $locale1->setLocale('en');

        $content1 = new Content();
        $content1->setSlug('index');
        $content1->setLocale($locale1);

        $locale2 = new Locale();
        $locale2->setLocale('nl');

        $content2 = new Content();
        $content2->setSlug('nl');
        $content2->setLocale($locale2);

        $locale3 = new Locale();
        $locale3->setLocale('fr');

        $content3 = new Content();
        $content3->setSlug('fr/custom-page');
        $content3->setLocale($locale3);

        $translationGroup = new TranslationGroup();
        $translationGroup->setContents(new ArrayCollection([$content1, $content2, $content3]));

        $actual = $translationGroup->getRouteMapping();

        $this->assertEquals($actual, [
            'en' => '/',
            'nl' => '/nl',
            'fr' => '/fr/custom-page'
        ]);
    }
}
