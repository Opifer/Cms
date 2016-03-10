<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * SectionBlock
 *
 * @ORM\Entity
 */
class SectionBlock extends CompositeBlock implements BlockContainerInterface
{

    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", name="header", nullable=true)
     */
    protected $header;

    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", name="footer", nullable=true)
     */
    protected $footer;

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
        return 'section';
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
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param string $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
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