<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * CardBlock
 *
 * @ORM\Entity
 */
class CardBlock extends CompositeBlock implements BlockContainerInterface
{

    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", name="header", nullable=true)
     */
    protected $header;

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
    public function getBlockType()
    {
        return 'card';
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
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
}