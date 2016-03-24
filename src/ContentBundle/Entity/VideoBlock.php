<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Entity\Block;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * VideoBlock
 *
 * @ORM\Entity
 */
class VideoBlock extends Block
{
    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="string", name="title", nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var integer
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", name="width", nullable=true)
     */
    protected $width;

    /**
     * @var integer
     *
     * @Revisions\Revised
     * @ORM\Column(type="integer", name="height", nullable=true)
     */
    protected $height;

    /**
     * @var MediaInterface
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Model\MediaInterface", fetch="EAGER")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $media;

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
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param MediaInterface $media
     *
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }



    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'video';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return VideoBlock
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }



    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return VideoBlock
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }


    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     *
     * @return VideoBlock
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

}
