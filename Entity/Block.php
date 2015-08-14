<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * Block
 *
 * @ORM\Table(name="block")
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\BlockRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 *
 * The discriminatorMap is handled dynamically by the BlockDiscriminatorListener. It
 * retrieves the mapped classes from the BlockManager and adds them to the
 * map.
 * @see Opifer\CmsBundle\EventListener\BlockDiscriminatorListener
 *
 * @JMS\ExclusionPolicy("all")
 */
abstract class Block implements BlockInterface
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
     * @var ContentInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface", inversedBy="blocks", cascade={})
     * @ORM\JoinColumn(name="owner_content_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $ownerContent;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Template", inversedBy="blocks", cascade={})
     * @ORM\JoinColumn(name="owner_template_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $ownerTemplate;

    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="children", cascade={})
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Block", mappedBy="parent")
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $children;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $position;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $sort;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $level;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $properties;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * @return BlockContainerInterface
     */
    public function getOwner()
    {
        return $this->getOwnerTemplate() ?: $this->getOwnerContent();
    }

    /**
     * @param BlockContainerInterface $owner
     */
    public function setOwner(BlockContainerInterface $owner)
    {
        if ($owner instanceof ContentInterface) {
            $this->setOwnerContent($owner);
        } elseif ($owner instanceof Template) {
            $this->setOwnerTemplate($owner);
        }
    }

    /**
     * @return BlockInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param BlockInterface $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Add child
     *
     * @param  BlockInterface $block
     *
     * @return BlockInterface
     */
    public function addChild(BlockInterface $block)
    {
        $this->children[] = $block;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return ContentInterface
     */
    public function getOwnerContent()
    {
        return $this->ownerContent;
    }

    /**
     * @param ContentInterface $content
     */
    public function setOwnerContent($content)
    {
        $this->ownerContent = $content;
    }

    /**
     * @return Template
     */
    public function getOwnerTemplate()
    {
        return $this->ownerTemplate;
    }

    /**
     * @param Template $ownerTemplate
     */
    public function setOwnerTemplate($ownerTemplate)
    {
        $this->ownerTemplate = $ownerTemplate;
    }
}