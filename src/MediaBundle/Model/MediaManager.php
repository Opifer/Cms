<?php

namespace Opifer\MediaBundle\Model;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class MediaManager implements MediaManagerInterface
{
    protected $em;
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function createMedia()
    {
        $class = $this->getClass();
        $media = new $class();

        return $media;
    }

    /**
     * {@inheritdoc}
     */
    public function save(MediaInterface $media)
    {
        $this->em->persist($media);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(MediaInterface $media)
    {
        $this->em->remove($media);
        $this->em->flush();
    }

    /**
     * Get paginated media items by request.
     *
     * @param Request $request
     *
     * @return Pagerfanta
     */
    public function getPaginatedByRequest(Request $request)
    {
        $qb = $this->getRepository()->createQueryBuilderFromRequest($request);
        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setMaxPerPage($request->get('limit', 50));
        $paginator->setCurrentPage($request->get('page', 1));

        return $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
    }
}
