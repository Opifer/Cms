<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Template
 *
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\TemplateRepository")
 * @ORM\Table(name="template")
 *
 * @GRID\Source(columns="id, name, displayName")
 */
class Template implements BlockInterface, BlockContainerInterface
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="ownerTemplate")
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $blocks;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
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
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param mixed $blocks
     */
    public function setBlocks($blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildBlocks()
    {
        $blocks = $this->getBlocks();
        $children = array();

        foreach ($blocks as $block) {
            if ($block->getParent()) {
                continue;
            }
            array_push($children, $block);
        }

        return new ArrayCollection($children);
    }

    /**
     * Add Block
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