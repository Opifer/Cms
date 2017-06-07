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

    /** @var RequestStack */
    protected $requestStack;

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
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
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
            $host = $this->requestStack->getCurrentRequest()->getHost();
            $domain = $this->em->getRepository(Domain::class)->findOneBy(['domain' => $host]);

            $this->site = $this->getRepository()->find($domain->getSite());
        }

        return $this->site;
    }
}
