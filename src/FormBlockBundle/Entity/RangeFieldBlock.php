<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Range Field Block
 *
 * @ORM\Entity
 */
class RangeFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'range_field';
    }
}
