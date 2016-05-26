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
     * Find related content.
     *
     * @param Content $content
     * @param int     $limit
     *
     * @return ArrayCollection
     */
    public function findRelated(Content $content, $limit = 10)
    {
        $city = $content->getValueSet()->getValueFor('address')->getAddress()->getCity();
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.valueSet', 'vs')
            ->innerJoin('vs.values', 'v')
            ->innerJoin('v.attribute', 'a')
            ->innerJoin('v.address', 'addr')
            ->where('addr.city = :city')
            ->andWhere('c.active = 1')
            ->andWhere('c.nestedIn IS NULL')
            ->andWhere('c.id <> :id')
            ->setParameter('id', $content->getId())
            ->setParameter('city', $city)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

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
     * @return ArrayCollection
     */
    public function findActiveAddressable()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.directory', 'directory')
            ->Andwhere('c.active = :active')
            ->setParameter('active', '1')
            ->orderBy('directory.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
