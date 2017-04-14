<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * ImageBlock
 *
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class ImageBlock extends Block
{
    /**
     * @var MediaInterface
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Model\MediaInterface", fetch="EAGER")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail"})
     */
    protected $media;

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
        return 'image';
    }
}
