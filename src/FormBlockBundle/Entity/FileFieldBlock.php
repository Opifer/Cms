<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * File Field Block
 *
 * @ORM\Entity
 */
class FileFieldBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'file_field';
    }
}
