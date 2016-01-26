<?php

namespace Opifer\EavBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Schema Repository
 */
class SchemaRepository extends EntityRepository
{
    /**
     * Find schemas by request
     *
     * @param Request $request
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findByRequest(Request $request)
    {
        $qb = $this->createQueryBuilder('s');

        if ($request->get('attribute')) {
            $qb->join('s.allowedInAttributes', 'a')
                ->andWhere('a.id = :attributeId')
                ->setParameter('attributeId', $request->get('attribute'));
        }

        return $qb->getQuery()->getArrayResult();
    }
}
