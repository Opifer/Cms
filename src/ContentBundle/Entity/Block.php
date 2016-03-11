<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\Revisions\DraftInterface;

use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Block\DraftVersionInterface;
use Opifer\ContentBundle\Block\VisitorInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\Content;
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
 * @Revisions\Revision(draft=true)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMS\ExclusionPolicy("all")
 */
abstract class Block implements BlockInterface, DraftInterface
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
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Model\ContentInterface", cascade={}, inversedBy="blocks")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $content;

    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Template", cascade={}, inversedBy="blocks")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $template;

    /**
     * @var BlockInterface
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Block", cascade={}, inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\Block", mappedBy="parent", cascade={"detach", "persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     **/
    protected $children;

    /**
     * @var integer
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position = 0;

    /**
     * @var integer
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sort = 0;

    /**
     * @var null|integer
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
     * @var boolean
     */
    protected $draft = true;

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
        } else if ($owner instanceof Template) {
            $this->template = $owner;
        } else if ($owner === null) {
            $this->template = null;
            $this->content = null;
        } else {
            throw new \Exception(sprintf('BlockInterface owner can only be of type Content or Template, not the provided %s type.', get_class($owner)));
        }

        return $this;
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

    /**
     * @return BlockInterface
     */
    protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param ContentInterface $template
     *
     * @return Block
     */
    protected function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return BlockInterface
     */
    protected function getContent()
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
     * @return boolean
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * @param boolean $draft
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

}