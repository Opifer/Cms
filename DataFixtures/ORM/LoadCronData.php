<?php

namespace Opifer\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Opifer\CmsBundle\Entity\Cron;

/**
 * Adds default cronjobs to the database
 */
class LoadCronData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $sitemapCron = new Cron();
        $sitemapCron->setCommand('presta:sitemaps:dump');
        $sitemapCron->setExpression('7 * * * *');
        $sitemapCron->setPriority(1);
        $sitemapCron->setState(Cron::STATE_PENDING);
        $manager->persist($sitemapCron);

        $manager->flush();
    }
}
