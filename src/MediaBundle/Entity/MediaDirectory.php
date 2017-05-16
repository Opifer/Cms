<?php

namespace Opifer\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * @ORM\Entity(repositoryClass="Opifer\MediaBundle\Repository\MediaDirectoryRepository")
 * @ORM\Table(name="media_directory")
 * @JMS\ExclusionPolicy("all")
 */
class MediaDirectory implements MediaDirectoryInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var ArrayCollection|MediaInterface[]
     *
     * @JMS\Expose
     * @JMS\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="Opifer\MediaBundle\Model\MediaInterface", mappedBy="directory")
     */
    protected $items;

    /**
     * @var MediaDirectoryInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Entity\MediaDirectoryInterface", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @var ArrayCollection|MediaDirectoryInterface[]
     *
     * @JMS\Expose
     * @JMS\MaxDepth(2)
     *
     * @ORM\OneToMany(targetEntity="Opifer\MediaBundle\Entity\MediaDirectoryInterface", mappedBy="parent")
     */
    protected $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return MediaDirectoryInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection|MediaInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection|MediaInterface[] $items
     *
     * @return MediaDirectoryInterface
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param MediaInterface $media
     *
     * @return $this
     */
    public function addItem(MediaInterface $media)
    {
        $this->items[] = $media;

        return $this;
    }

    /**
     * @param MediaInterface $media
     *
     * @return $this
     */
    public function removeItem(MediaInterface $media)
    {
        $this->items->removeElement($media);

        return $this;
    }

    /**
     * @return int|null
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("parent_id")
     */
    public function getParentId()
    {
        return ($this->getParent()) ? $this->getParent()->getId() : null;
    }

    /**
     * @return MediaDirectory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param MediaDirectory $parent
     *
     * @return MediaDirectory
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ArrayCollection|MediaDirectory[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection|MediaDirectory[] $children
     *
     * @return MediaDirectory
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @param MediaDirectory $child
     *
     * @return $this
     */
    public function addChild(MediaDirectory $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param MediaDirectory $child
     *
     * @return $this
     */
    public function removeChild(MediaDirectory $child)
    {
        $this->children->removeElement($child);

        return $this;
    }
}
