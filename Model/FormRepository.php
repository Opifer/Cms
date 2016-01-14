<?php

namespace Opifer\FormBundle\Model;

use Doctrine\ORM\EntityRepository;

class FormRepository extends EntityRepository
{
    /**
     * Find all forms, joined by it's posts
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllWithPosts()
    {
        return $this->createQueryBuilder('f')
            ->select('f', 'p')
            ->leftJoin('f.posts', 'p')
            ->orderBy('f.name')
            ->getQuery()
            ->getResult();
    }
}
