<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Form Field Block
 *
 * @ORM\Entity
 */
class FormFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'form_field';
    }
}
