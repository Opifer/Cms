<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarouselBlock
 *
 * @ORM\Entity
 */
class CarouselBlock extends CompositeBlock
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'carousel';
    }
}
