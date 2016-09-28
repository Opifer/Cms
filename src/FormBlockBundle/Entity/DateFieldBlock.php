<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Date Field Block
 *
 * @ORM\Entity
 */
class DateFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'date_field';
    }
}
