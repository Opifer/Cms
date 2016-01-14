<?php

namespace Opifer\EavBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\EavBundle\Model\ValueSetInterface;

/**
 * ValueRepository
 */
class ValueRepository extends EntityRepository
{
    public function getSortedValuesBySet(ValueSetInterface $valueSet)
    {
        $query = $this->createQueryBuilder('v')
            ->join('v.attribute', 'a')
            ->where('v.valueSet = "valueset')
            ->orderBy('a.sort', 'DESC')
            ->setParameter('valueset', $valueSet)
            ->getQuery()
        ;

        return $query->getResult();
    }
}
