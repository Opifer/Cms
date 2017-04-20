<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\CompositeBlock;

/**
 * FormSectionBlock
 *
 * @ORM\Entity
 */
class FormSectionBlock extends CompositeBlock
{

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'form_section';
    }
}
