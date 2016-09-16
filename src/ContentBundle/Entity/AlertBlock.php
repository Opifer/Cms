<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Alert Block
 *
 * @ORM\Entity
 */
class AlertBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'alert';
    }
}
