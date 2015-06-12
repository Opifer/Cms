<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Opifer\CmsBundle\Entity\Template;

class LoadTemplateData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $template = new Template();
        $template->setObjectClass('Opifer\CmsBundle\Entity\Content');
        $template->setName('page');
        $template->setDisplayName('Page');

        $presentation = '{"id":'.$this->getReference("page-layout")->getId().',"name":"page", "filename":"OpiferCmsBundle:Layout:page.html.twig"}';
        $presentation = json_encode(json_decode($presentation, true));
        $template->setPresentation($presentation);

        $manager->persist($template);
        $this->addReference('page-template', $template);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
