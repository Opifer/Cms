<?php


namespace Opifer\EavBundle\Repository;


use Doctrine\ORM\EntityRepository;

class AttributeRepository extends EntityRepository {

    public function findByName($name) {
        $query = $this->createQueryBuilder('o')
            ->innerJoin('o.attribute', 'a')
            ->where('a.name = :name')
            ->setParameters([
                'name'     => $name,
            ])
            ->getQuery()
        ;

        return $query->getSingleResult();
    }
}