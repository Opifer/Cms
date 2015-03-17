<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\MediaInterface;

/**
 * MediaValue
 *
 * @ORM\Entity
 */
class MediaValue extends Value implements \IteratorAggregate, \Countable
{
    /**
     * @ORM\ManyToMany(targetEntity="Opifer\EavBundle\Model\MediaInterface")
     * @ORM\JoinTable(name="value_media",
     *      joinColumns={@ORM\JoinColumn(name="value_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="cascade")}
     * )
     */
    protected $medias;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->medias = new ArrayCollection();
    }

    /**
     * Get the value
     *
     * Overrides the parent getValue method
     *
     * @return ArrayCollection
     */
    public function getValue()
    {
        return $this->medias;
    }

    /**
     * Makes it possible to loop over this entity.
     *
     * @return ArrayCollection
     */
    public function getIterator()
    {
        return $this->medias;
    }

    /**
     * Add media
     *
     * @param  MediaInterface $media
     * @return Value
     */
    public function addMedia(MediaInterface $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * Remove medias
     *
     * @param MediaInterface $media
     */
    public function removeMedia(MediaInterface $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * Set Medias
     *
     * @param ArrayCollection $medias
     */
    public function setMedias(ArrayCollection $medias)
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (count($this->medias) < 1) ? true : false;
    }

    /**
     * Count the amount of mapped media items
     *
     * @return int
     */
    public function count()
    {
        return count($this->medias);
    }
}
