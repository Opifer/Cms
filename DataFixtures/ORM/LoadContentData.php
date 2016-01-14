<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Opifer\CmsBundle\Entity\Content;

/**
 * Class LoadContentData
 *
 * @package Opifer\CmsBundle\DataFixtures\ORM
 */
class LoadContentData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $site = $manager->getRepository('OpiferCmsBundle:Site')->findOneBy([]);

        $eavManager = $this->container->get('opifer.eav.eav_manager');

        $content = $eavManager->initializeEntity($this->getReference('page-template'));
        $content->setSite($site);
        $content->setTitle('Pagina niet gevonden');
        $content->setSearchable(false);
        $content->setIndexable(false);
        $content->setActive(true);
        $content->setSlug('404');
        $content->setAuthor($this->getReference('admin'));
        $content->replaceMissingAttributes();
        $content->getValueSet()->get('content')->setValue('<p><span style="font-size:18px">Page not found</span></p><p>The page you were looking for could not be found</p>');

        $manager->persist($content);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
