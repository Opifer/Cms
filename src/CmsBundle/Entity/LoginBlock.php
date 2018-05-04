<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
