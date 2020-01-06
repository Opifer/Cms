<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\Revisions\DraftInterface;
use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Block\VisitorInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\Content;
use Opifer\ContentBundle\Model\ContentInterface;

/**
 * Block.
 *
 * @ORM\Table(name="block")
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\BlockRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 *
 * The discriminatorMap is handled dynamically by the BlockDiscriminatorListener. It
 * retrieves the mapped classes from the BlockManager and adds them to the
 * map.
 *
 * @see Opifer\CmsBundle\EventListener\BlockDiscriminatorListener
 *
 * @Revisions\Revision(draft=true)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @GRID\Source(columns="id, name, displayName")
 *
 * @JMS\ExclusionPolicy("all")
 */
abstract class Block implements BlockInterface, DraftInterface
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "details"})
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "details"})
     * @Revisions\Revised
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "details"})
     * @Revisions\Revised
     * @ORM\Column(name="display_name", type="string", nullable=true)
     */
    protected $displayName;

    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface", inversedBy="blocks")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $content;

    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Template", inversedBy="blocks")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $template;

    /**
     * @var BlockInterface
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Block", cascade={"detach"}, inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @JMS\Expose
     * @JMS\Groups({"tree"})
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="parent", cascade={"detach", "persist"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    protected $children;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail"})
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position = 0;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail", "list"})
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sort = 0;

    /**
     * @var null|int
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail", "list"})
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", nullable=true, options={"default":null})
     */
    protected $sortParent = null;

    /**
     * @var array
     *
     * @Revisions\Revised
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $properties;

    /**
     * @var bool
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail", "list"})
     *
     * @ORM\Column(type="boolean")
     */
    protected $shared = false;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail", "list"})
     *
     * @ORM\Column(type="string", nullable=true)
     * @GRID\Column(title="label.shared_name")
     */
    protected $sharedName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @GRID\Column(title="label.shared_display_name")
     */
    protected $sharedDisplayName;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail"})
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", name="value", nullable=true)
     */
    protected $value;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="create")
     * @Revisions\Revised
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @Gedmo\Timestampable(on="update")
     * @Revisions\Revised
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @Revisions\Revised
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var bool
     *
     * @JMS\Expose
     *
     * @Revisions\Revised
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = true;

    /**
     * @var bool
     */
    protected $draft = false;

    /**
     * Constructor.
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
        return sprintf('Block %d', $this->id);
    }

    public function isInRoot()
    {
        if (!$this->getOwner()) {
            return false;
        } elseif (!$this->getParent()) {
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
     * @return ContentInterface
     */
    public function getOwner()
    {
        return ($this->getContent()) ? $this->content : $this->template;
    }

    /**
     * @param ContentInterface $owner
     *
     * @return BlockInterface
     */
    public function setOwner(ContentInterface $owner = null)
    {
        if ($owner instanceof Content) {
            $this->content = $owner;
        } elseif ($owner instanceof Template) {
            $this->template = $owner;
        } elseif ($owner === null) {
            $this->template = null;
            $this->content = null;
        } else {
            throw new \Exception(sprintf('BlockInterface owner can only be of type Content or Template, not the provided %s type.', get_class($owner)));
        }

        return $this;
    }

    public function getOwnerName()
    {
        $owner = $this->getOwner();

        if ($owner === null) {
            return;
        } elseif ($owner instanceof Content) {
            return $owner->getTitle();
        } elseif ($owner instanceof Template) {
            return $owner->getName();
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
     * @JMS\VirtualProperty
     * @JMS\SerializedName("parentId")
     * @JMS\Groups({"tree", "detail", "list"})
     */
    public function getParentId()
    {
        return ($this->parent) ? $this->parent->getId() : null;
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
     * @JMS\VirtualProperty
     * @JMS\SerializedName("properties")
     *
     * @return array
     */
    public function getProperties()
    {
        return array_merge($this->properties);
    }

    /**
     *
     * @param array $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param string $key
     */
    public function getProperty($key)
    {
        return (isset($this->properties[$key])) ? $this->properties[$key] : null;
    }

    /**
     * Set created at.
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $date = null)
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * Get created at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Set deletedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function getChildren()
    {
        return new ArrayCollection();
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
     *
     * @return Block
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if (!$this->displayName) {
            return ucfirst(str_replace('_', ' ', $this->name));
        }

        return $this->displayName;
    }

    /**
     * @param string $displayName
     *
     * @return Block
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
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
     * @return Block
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
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
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
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
     * Checks if one of the current blocks' parents is a shared block.
     *
     * @return bool
     */
    public function hasSharedParent()
    {
        $parent = $this->getParent();

        if ($parent != null && ($parent->isShared() || $parent->hasSharedParent())) {
            return true;
        }

        return false;
    }

    /**
     * @param VisitorInterface $visitor
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }

    /**
     * @return BlockInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param ContentInterface $template
     *
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("templateId")
     * @JMS\Groups({"tree", "detail", "list"})
     */
    public function getTemplateId()
    {
        return ($this->template) ? $this->template->getId() : null;
    }

    /**
     * @return BlockInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return Block
     */
    protected function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("contentId")
     * @JMS\Groups({"tree", "detail", "list"})
     */
    public function getContentId()
    {
        return ($this->content) ? $this->content->getId() : null;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * @param bool $draft
     *
     * @return Block
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;

        return $this;
    }

    public function hasChildren()
    {
        return false;
    }

    /**
     * @JMS\Groups({"tree", "detail"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("type")
     *
     * @return string
     */
    public function getDiscriminator()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return $this->active;
    }
}
