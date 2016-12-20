<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use JMS\Serializer\Annotation as JMS;

/**
 * DataViewBlock.
 *
 * @ORM\Entity
 */
class DataViewBlock extends CompositeBlock
{
    /**
     * @var DataView
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\DataView", fetch="EAGER")
     * @ORM\JoinColumn(name="data_view_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $dataView;

    /**
     * @var ArrayCollection|MediaInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Opifer\MediaBundle\Model\MediaInterface", fetch="EAGER")
     * @ORM\JoinTable(name="opifer_block_media",
     *     joinColumns={@ORM\JoinColumn(name="block_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id")}
     *     )
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
     * @return string
     */
    public function getBlockType()
    {
        return 'data_view';
    }

    /**
     * @return DataView
     */
    public function getDataView()
    {
        return $this->dataView;
    }

    /**
     * @param DataView $dataView
     *
     * @return $this
     */
    public function setDataView($dataView)
    {
        $this->dataView = $dataView;

        return $this;
    }

    /**
     * @return ArrayCollection|MediaInterface[]
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param ArrayCollection|MediaInterface[] $medias
     *
     * @return DataViewBlock
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * @param MediaInterface $media
     *
     * @return DataViewBlock
     */
    public function addMedia(MediaInterface $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @param MediaInterface $media
     *
     * @return DataViewBlock
     */
    public function removeMedia(MediaInterface $media)
    {
        $this->medias->removeElement($media);

        return $this;
    }

    /**
     * @JMS\Groups({"tree", "detail"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("type")
     *
     * @return string
     */
    public function getDiscriminator()
    {
        if ($this->dataView->getViewReference()) {
            return $this->dataView->getName();
        }

        return parent::getDiscriminator();
    }
}
