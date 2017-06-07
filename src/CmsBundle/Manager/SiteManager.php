<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Opifer\CmsBundle\Entity\Domain;
use Opifer\CmsBundle\Entity\Site;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Site Manager.
 */
class SiteManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /** @var DomainManager */
    protected $domainManager;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var Site
     */
    protected $site;

    /**
     * SiteManager constructor.
     *
     * @param EntityManager $em
     * @param DomainManager $domainManager
     */
    public function __construct(EntityManager $em, DomainManager $domainManager)
    {
        $this->em = $em;
        $this->domainManager = $domainManager;
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

    public function getSite()
    {
        if ($this->site === null) {

            $domain = $this->domainManager->getDomain();
            $this->site = $this->getRepository()->find($domain->getSite());
        }

        return $this->site;
    }
}
