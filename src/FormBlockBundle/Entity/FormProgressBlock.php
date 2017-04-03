<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Form progress Block
 *
 * @ORM\Entity
 */
class FormProgressBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'form_progress';
    }
}
