<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Opifer\CmsBundle\Entity\Schema;

class LoadSchemaData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $schema = new Schema();
        $schema->setObjectClass('Opifer\CmsBundle\Entity\Content');
        $schema->setName('page');
        $schema->setDisplayName('Page');

        $presentation = '{"id":'.$this->getReference("page-layout")->getId().',"name":"page", "filename":"OpiferCmsBundle:Layout:page.html.twig"}';
        $presentation = json_encode(json_decode($presentation, true));
        $schema->setPresentation($presentation);

        $manager->persist($schema);
        $this->addReference('page-schema', $schema);
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
