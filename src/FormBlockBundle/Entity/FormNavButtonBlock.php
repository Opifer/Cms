<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Form nav button Block
 *
 * @ORM\Entity
 */
class FormNavButtonBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'form_nav_button';
    }
}
