<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * ContainerBlock
 *
 * @ORM\Entity
 */
class ContainerBlock extends CompositeBlock implements BlockContainerInterface
{

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="value", nullable=true)
     */
    protected $wrapper;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Block %d: %s container", $this->id, $this->wrapper);
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'container_' . $this->wrapper;
    }

    /**
     * @return wrapper
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * @param wrapper $wrapper
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }


}