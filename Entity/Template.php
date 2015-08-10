<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Template
 *
 * @ORM\Entity
 * @ORM\Table(name="template")
 *
 * @GRID\Source(columns="id, name, displayName")
 */
class Template extends LayoutBlock
{
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @GRID\Column(title="label.name")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @GRID\Column(title="label.display_name")
     */
    protected $displayName;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'template';
    }
}