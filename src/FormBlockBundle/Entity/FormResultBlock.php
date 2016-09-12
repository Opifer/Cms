<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Form Result Block
 *
 * @ORM\Entity
 */
class FormResultBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'form_result';
    }
}
