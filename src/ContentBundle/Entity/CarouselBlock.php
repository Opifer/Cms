<?php

namespace Opifer\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
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
