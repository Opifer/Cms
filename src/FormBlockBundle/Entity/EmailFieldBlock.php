<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Email Field Block
 *
 * @ORM\Entity
 */
class EmailFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'email_field';
    }
}
