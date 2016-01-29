<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
