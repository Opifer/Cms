<?php

namespace Opifer\CmsBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository\RepositoryFactory;
use Opifer\CmsBundle\Entity\Domain;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * DomainManager
 */
class DomainManager
{
    /** @var EntityManager */
    protected $em;

    /** @var string */
    protected $host;

    /** @var Domain */
    protected $domain;

    /** @var RepositoryFactory */
    protected $repository;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * DomainManager constructor.
     *
     * @param EntityManager $entityManager
     * @param RequestStack $requestStack
     */
    public function __construct(EntityManager $entityManager, RequestStack $requestStack)
    {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        if (!$this->host) {
            $this->host = $this->requestStack->getCurrentRequest()->getHost();
        }

        return $this->host;
    }

    /**
     * @return Domain
     */
    public function getDomain()
    {
        if (!$this->domain) {
            $domain = $this->getRepository()->findOneBy(['domain' => $this->getHost()]);
            if ($domain) {
                $this->domain = $domain;
            }
        }

        return $this->domain;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = $this->em->getRepository(Domain::class);
        }

        return $this->repository;
    }
}
