<?php

namespace Opifer\EavBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\EavBundle\Entity\ValueSet;

/**
 * ValueRepository
 */
class ValueRepository extends EntityRepository
{
    public function getSortedValuesBySet(ValueSet $valueSet)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT v
            FROM OpiferEavBundle:Value v
            JOIN v.attribute a
            WHERE v.valueSet = :valueSet
            ORDER BY a.sort ASC'
        )->setParameter('valueSet', $valueSet->getId());

        return $query->getResult();
    }
}
