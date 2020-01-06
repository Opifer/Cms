<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * ContainerBlock
 *
 * @ORM\Entity
 */
class ContainerBlock extends CompositeBlock implements BlockContainerInterface
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'container';
    }
}
