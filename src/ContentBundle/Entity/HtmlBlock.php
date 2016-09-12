<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;

/**
 * HtmlBlock
 *
 * @ORM\Entity
 */
class HtmlBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'html';
    }
}
