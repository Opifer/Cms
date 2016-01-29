<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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

    /** @var array */
    protected $tree;

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

    /**
     * @return array
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param array $tree
     *
     * @return NavigationBlock
     */
    public function setTree($tree)
    {
        $this->tree = $tree;

        return $this;
    }
}
