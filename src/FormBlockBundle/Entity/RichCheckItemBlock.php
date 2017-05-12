<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Entity\Block;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\Revisions\Mapping\Annotation as Revisions;

/**
 * Rich checkbox-like block.
 *
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class RichCheckItemBlock extends Block
{
    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail"})
     */
    protected $title;

    /**
     * @var MediaInterface
     *
     * @Revisions\Revised
     *
     * @JMS\Expose
     * @JMS\Groups({"tree", "detail"})
     */
    protected $media;

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
     * @return RichCheckItemBlock
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
        return 'rich_check_item';
    }
}
