<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Opifer\CmsBundle\Entity\Attribute;

class LoadAttributeData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $attribute = new Attribute();
        $attribute->setName('content');
        $attribute->setDisplayName('Content');
        $attribute->setValueType('html');
        $attribute->setTemplate($this->getReference('page-template'));
        $attribute->setSort(10);

        $manager->persist($attribute);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
