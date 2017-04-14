<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Text Field Block
 *
 * @ORM\Entity
 */
class TextFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'text_field';
    }
}
