<?php

namespace Opifer\CmsBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentRepository as BaseContentRepository;

/**
 * ContentRepository.
 *
 * Because content items are used in different kinds of use-cases, please specify
 * the scope of the function inside the function name.
 * For example:
 *
 * findByIds()         => returns all content
 * findPublicByIds()   => returns only public content
 * findPrivateByIds()  => returns only private content
 */
class ContentRepository extends BaseContentRepository
{
    /**
     * Find the last updated content items.
     *
     * @param int $limit
     *
     * @return ArrayCollection
     */
    public function findLastUpdated($limit = 5)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.updatedAt', 'DESC')
            ->where('c.layout = :layout')
            ->setParameter(':layout', false)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Find the last created content items.
     *
     * @param int $limit
     *
     * @return ArrayCollection
     */
    public function findLastCreated($limit = 5)
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Find all active and addressable content items.
     *
     * @return ArrayCollection|array
     */
    public function findIndexable()
    {
        return $this->createQueryBuilder('c')
            ->where('c.indexable = :indexable')
            ->Andwhere('c.active = :active')
            ->Andwhere('c.layout = :layout')
            ->setParameters([
                'active' => true,
                'layout' => false,
                'indexable' => true
            ])
            ->orderBy('c.slug', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
