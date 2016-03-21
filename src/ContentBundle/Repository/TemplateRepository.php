<?php

namespace Opifer\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Provider\BlockProviderInterface;

/**
 * Class TemplateRepository
 *
 * @package Opifer\ContentBundle\Model
 */
class TemplateRepository extends EntityRepository implements BlockProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getBlockOwner($id)
    {
        return $this->find($id);
    }
}