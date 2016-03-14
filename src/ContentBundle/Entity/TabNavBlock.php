<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * TabNavBlock
 *
 * @ORM\Entity
 */
class TabNavBlock extends CompositeBlock implements BlockContainerInterface
{

    protected $properties = ['tabs' => array()];

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Block %d: TabNav", $this->id);
    }


    public function getTabs()
    {
        return $this->properties['tabs'];
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'tabnav';
    }

}