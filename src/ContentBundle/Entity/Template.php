<?php

namespace Opifer\ContentBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;

/**
 * Template
 *
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\TemplateRepository")
 * @ORM\Table(name="template")
 *
 * @GRID\Source(columns="id, name, displayName")
 */
class Template implements ContentInterface
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
     * @ORM\Column(type="string")
     *
     * @GRID\Column(title="label.name")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="template", cascade={"detach", "persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $blocks;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer")
     */
    protected $version = 0;


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
     * Get all blocks
     *
     * @return ArrayCollection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Set blocks
     *
     * @param mixed $blocks
     */
    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * Add block
     *
     * @param BlockInterface $block
     *
     * @return BlockInterface
     */
    public function addBlock(BlockInterface $block)
    {
        $this->blocks[] = $block;

        return $this;
    }

    /**
     * Remove block
     *
     * @param BlockInterface $block
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Check if any blocks are set
     *
     * @return boolean
     */
    public function hasBlocks()
    {
        return (count($this->getBlocks())) ? true : false;
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

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     * @return Content
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
}