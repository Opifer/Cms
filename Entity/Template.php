<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;

use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Template
 *
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\TemplateRepository")
 * @ORM\Table(name="template")
 *
 * @GRID\Source(columns="id, name, displayName")
 */
class Template
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
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
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @GRID\Column(title="label.view")
     */
    protected $view;

    /**
     * @var Template
     *
     * @ORM\OneToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\BlockInterface")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     **/
    protected $block;


    /**
     * Constructor
     */
    public function __construct()
    {
    }

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
    public function getBlockType()
    {
        return 'root';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Template
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Template $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param BlockInterface $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}