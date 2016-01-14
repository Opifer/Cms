<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class DocumentBlock
 *
 * @ORM\Entity
 *
 * @package Opifer\ContentBundle\Entity
 */
class DocumentBlock extends CompositeBlock implements BlockContainerInterface, BlockOwnerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBlockType()
    {
        return 'document';
    }

}