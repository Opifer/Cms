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
}
