<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Entity\Block;

/**
 * LoginBlock.
 *
 * @ORM\Entity
 */
class LoginBlock extends Block
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'login';
    }
}
