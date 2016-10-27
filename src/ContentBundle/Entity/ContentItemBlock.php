<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;

/**
 * ContentItem Block.
 *
 * @ORM\Entity
 */
class ContentItemBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'content_item';
    }
}
