<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected $userManager;

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
        $this->userManager = $this->container->get('fos_user.user_manager');
        $class = $this->userManager->getClass();

        $admin = new $class;
        $admin->setUsername($this->container->getParameter('admin_username'));
        $admin->setEmail($this->container->getParameter('admin_email'));
        $admin->setPlainPassword($this->container->getParameter('admin_password'));
        $admin->setEnabled('true');
        $admin->addRole('ROLE_SUPER_ADMIN');

        $manager->persist($admin);
        $this->addReference('admin', $admin);
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
