<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Opifer\CmsBundle\Entity\Site;
use Doctrine\ORM\EntityRepository;

/**
 * Site Manager.
 */
class SiteManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * SiteManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array|Site[]
     */
    public function getSites()
    {
        $sites = $this->getRepository()->findAll();

        $siteData = [];
        foreach ($sites as $site) {
            $siteData[$site->getId()]['id'] = $site->getId();
            $siteData[$site->getId()]['name'] = $site->getName();
            $siteData[$site->getId()]['description'] = $site->getDescription();
            $siteData[$site->getId()]['domain'] = $site->getDomains()->first()->getDomain();
        }

        return $siteData;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->em->getRepository(Site::class);
        }

        return $this->repository;
    }
}
