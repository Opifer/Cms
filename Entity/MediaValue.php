<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * MediaValue
 *
 * @ORM\Entity
 */
class MediaValue extends Value implements ValueInterface, \IteratorAggregate
{
    /**
     * @ORM\ManyToMany(targetEntity="Opifer\CmsBundle\Entity\Media")
     * @ORM\JoinTable(name="value_media",
     *      joinColumns={@ORM\JoinColumn(name="value_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id")}
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
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIterator()
    {
        return $this->medias;
    }

    /**
     * Add medias
     *
     * @param  MediaInterface $medias
     * @return Value
     */
    public function addMedia(MediaInterface $medias)
    {
        $this->medias[] = $medias;

        return $this;
    }

    /**
     * Remove medias
     *
     * @param MediaInterface $medias
     */
    public function removeMedia(MediaInterface $medias)
    {
        $this->medias->removeElement($medias);
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
}
