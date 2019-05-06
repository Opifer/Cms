<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * ModalBlock
 *
 * @ORM\Entity
 */
class ModalBlock extends CompositeBlock implements BlockContainerInterface
{
    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="string", nullable=true)
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ListBlock
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
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
     * @return string
     */
    public function getBlockType()
    {
        return 'modal';
    }
}