<?php

namespace Opifer\EavBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * OptionRepository
 */
class OptionRepository extends EntityRepository
{
    public function getOptionsForAttributeName($name)
    {
        $query = $this->createQueryBuilder('o')
            ->innerJoin('o.attribute', 'a')
            ->where('a.name = :name')
            ->setParameters([
                'name' => $name,
            ])
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Find all options by an array or comma-separated list of ids.
     *
     * @param array|string $ids
     *
     * @return array
     */
    public function findByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', trim($ids));
        }

        return $this->createQueryBuilder('o')
            ->where('o.id IN (:ids)')
            ->setParameters([
                'ids' => $ids,
            ])
            ->getQuery()
            ->getResult();
    }
}
