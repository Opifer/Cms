<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * ImageBlock
 *
 * @ORM\Entity
 * @ORM\Table(name="block_image")
 */
class ImageBlock extends Block
{
    /**
     * @var MediaInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\MediaBundle\Model\MediaInterface")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $image;

    /**
     * @return MediaInterface
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param MediaInterface $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'image';
    }
}