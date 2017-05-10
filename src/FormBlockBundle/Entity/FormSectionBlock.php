<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\Revisions\Mapping\Annotation as Revisions;

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
