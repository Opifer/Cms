<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IFrameBlock
 *
 * @ORM\Entity
 */
class IFrameBlock extends Block
{

    protected $properties = ['width' => '100%', 'height' => '500', 'border' => '0'];


    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'iframe';
    }
}