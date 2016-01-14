<?php

namespace Opifer\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * ButtonBlock
 *
 * @ORM\Entity
 */
class ButtonBlock extends Block
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
        return 'button';
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


}