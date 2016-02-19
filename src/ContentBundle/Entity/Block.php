<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\DraftVersionInterface;
use Opifer\ContentBundle\Block\VisitorInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;

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
 * @Gedmo\Loggable(logEntryClass="Opifer\ContentBundle\Entity\BlockLogEntry")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMS\ExclusionPolicy("all")
 */
abstract class Block implements BlockInterface, DraftVersionInterface
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
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Block", cascade={}, inversedBy="inheritedBy")
     * @ORM\JoinColumn(name="super_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $super;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="super")
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $inheritedBy;

    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Block", cascade={}, inversedBy="owning")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="owner")
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $owning;

    /**
     * @var BlockInterface
     *
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Block", cascade={}, inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $children;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="integer")
     */
    protected $position = 0;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="integer")
     */
    protected $sort = 0;

    /**
     * @var null|integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="integer", nullable=true, options={"default":null})
     */
    protected $sortParent = null;

    /**
     * @var array
     *
     * @Gedmo\Versioned
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $properties;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $shared = false;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sharedName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sharedDisplayName;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="version", type="integer")
     */
    protected $version = 0;

    /** @var integer */
    protected $rootVersion;

    /**
     * Flag to determine if we only create a logentry or not.
     *
     * @var bool
     */
    protected $publish = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Block %d", $this->id);
    }


    public function isInRoot()
    {
        if (! $this->getOwner()) {
            return false;
        } elseif (! $this->getParent()) {
            return true;
        }

        return $this->getParent()->getId() == $this->getOwner()->getId();
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
    public function getSuper()
    {
        return $this->super;
    }

    /**
     * @param BlockInterface $parent
     */
    public function setSuper($super)
    {
        $this->super = $super;
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
     * @return int|null
     */
    public function getSortParent()
    {
        return $this->sortParent;
    }

    /**
     * @param int $sortParent
     */
    public function setSortParent($sortParent)
    {
        $this->sortParent = $sortParent;
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

    /**
     * Set created at
     *
     * @param  \DateTime $date
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $date)
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at
     *
     * @param  \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Set deletedAt
     *
     * @param  \DateTime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function getChildren()
    {
        return new ArrayCollection;
    }

    /**
     * @return ArrayCollection
     */
    public function getOwning()
    {
        return $this->owning;
    }

    /**
     * @param ArrayCollection $owning
     */
    public function setOwning($owning)
    {
        $this->owning = $owning;
    }

    /**
     * Add owning
     *
     * @param BlockInterface $block
     *
     * @return BlockInterface
     */
    public function addOwning(BlockInterface $block)
    {
        $this->owning[] = $block;

        return $this;
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
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }


    /**
     * @return int
     */
    public function getRootVersion()
    {
        return $this->rootVersion;
    }

    /**
     * @param int $rootVersion
     */
    public function setRootVersion($rootVersion)
    {
        $this->rootVersion = $rootVersion;
    }

    /**
     * @return boolean
     */
    public function isPublish()
    {
        return $this->publish;
    }

    /**
     * @param boolean $publish
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;
    }

    /**
     * @return string
     */
    public function getSharedDisplayName()
    {
        return $this->sharedDisplayName;
    }

    /**
     * @param string $sharedDisplayName
     */
    public function setSharedDisplayName($sharedDisplayName)
    {
        $this->sharedDisplayName = $sharedDisplayName;
    }

    /**
     * @return boolean
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * @param boolean $shared
     */
    public function setShared($shared)
    {
        $this->shared = $shared;
    }

    /**
     * @return int
     */
    public function getSharedName()
    {
        return $this->sharedName;
    }

    /**
     * @param int $sharedName
     */
    public function setSharedName($sharedName)
    {
        $this->sharedName = $sharedName;
    }

    /**
     * @param VisitorInterface $visitor
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }
}