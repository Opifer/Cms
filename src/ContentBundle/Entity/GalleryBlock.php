<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * Gallery Block
 *
 * @ORM\Entity
 */
class GalleryBlock extends Block
{
    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var ArrayCollection
     */
    protected $gallery;

    public function __construct()
    {
        parent::__construct();

        $this->gallery = new ArrayCollection();
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
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param $gallery
     * @return $this
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get the gallery of actual media items
     *
     * @return MediaInterface[]|ArrayCollection
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'gallery';
    }
}
