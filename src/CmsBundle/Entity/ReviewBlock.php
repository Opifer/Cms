<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Entity\Block;

/**
 * Review Block.
 *
 * @ORM\Entity
 */
class ReviewBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'review';
    }
}
