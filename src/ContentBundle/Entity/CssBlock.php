<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * CssBlock
 *
 * @ORM\Entity
 */
class CssBlock extends Block
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'css';
    }
}