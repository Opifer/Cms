<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Number Field Block
 *
 * @ORM\Entity
 */
class NumberFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'number_field';
    }
}
