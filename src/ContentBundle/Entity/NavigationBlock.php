<?php

namespace Opifer\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Navigation Block
 *
 * @ORM\Entity
 */
class NavigationBlock extends Block
{
    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'navigation';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return NavigationBlock
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
