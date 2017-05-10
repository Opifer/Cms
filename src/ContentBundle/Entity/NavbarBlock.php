<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;

/**
 * Navigation Block
 *
 * @ORM\Entity
 */
class NavBarBlock extends CompositeBlock
{
    /**
     * {@inheritdoc}
     */
    public function getBlockType()
    {
        return 'navbar';
    }
}
