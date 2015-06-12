<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Opifer\CmsBundle\Entity\Layout;

class LoadLayoutData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $layout = new Layout();
        $layout
            ->setName('page')
            ->setDescription('Page Layout')
            ->setFilename('OpiferCmsBundle:Layout:page.html.twig')
        ;
        $manager->persist($layout);
        $this->addReference('page-layout', $layout);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
