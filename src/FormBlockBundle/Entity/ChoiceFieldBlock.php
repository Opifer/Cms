<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Choice Field Block
 *
 * @ORM\Entity
 */
class ChoiceFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'choice_field';
    }
}
