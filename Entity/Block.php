<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Block
 *
 * @ORM\Table(name="block")
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\BlockRepository")
 * @ORM\InheritanceType("JOINED")
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
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="owns", cascade={})
     */
    protected $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Block", mappedBy="owner")
     **/
    protected $owns;

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


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->owns = new ArrayCollection();
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
     * @return BlockInterface
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param BlockInterface $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getOwns()
    {
        return $this->owns;
    }

    /**
     * @param mixed $owns
     */
    public function setOwns($owns)
    {
        $this->owns = $owns;
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
}