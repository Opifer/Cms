<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\MediaBundle\Model\MediaInterface;

/**
 * DownloadsBlock
 *
 * @ORM\Entity
 */
class DownloadsBlock extends Block
{
    /**
     * @var ArrayCollection
     */
    protected $items;

    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection();
    }

    /**
     * @param $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $items;
    }

    /**
     * Get the items of actual media items
     *
     * @return MediaInterface[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'downloads';
    }
}
