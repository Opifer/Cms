<?php

namespace Opifer\MediaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MediaDirectoryRepository extends EntityRepository
{
    /**
     * Find directories by a parent ID.
     *
     * @param int|null $dir
     *
     * @return array
     */
    public function findByDirectory($dir = null)
    {
        $qb = $this->createQueryBuilder('d');

        if ($dir) {
            $query = $qb->leftJoin('d.parent', 'parent')
                ->where('parent.id = :parent')
                ->setParameter('parent', $dir);
        } else {
            $query = $qb->where('d.parent IS NULL');
        }

        return $query->addOrderBy('createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
