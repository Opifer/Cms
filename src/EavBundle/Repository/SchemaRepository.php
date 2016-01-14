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
        $qb = $this->createQueryBuilder('t');

        if ($request->get('name')) {
            $qb->andWhere('t.name = :name')->setParameter('name', $request->get('name'));
        }

        if ($request->get('attribute')) {
            $qb->join('t.allowedInAttributes', 'a')
                ->andWhere('a.id = :attributeId')
                ->setParameter('attributeId', $request->get('attribute'));
        }

        $qb->orderBy('t.displayName', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
