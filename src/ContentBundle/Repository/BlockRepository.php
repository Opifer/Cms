<?php

namespace Opifer\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BlockRepository
 *
 * @package Opifer\ContentBundle\Model
 */
class BlockRepository extends EntityRepository
{
	public function getContentByValue($search){
		
		return $this->createQueryBuilder('b')
				->select('c.title, c.slug')
				->innerjoin('b.content', 'c')
				->where('b.value LIKE :search')
				->setParameter('search', '%'.$search.'%')
				->getQuery()
				->getResult();
	}
}