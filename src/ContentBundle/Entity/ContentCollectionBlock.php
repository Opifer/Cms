<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Model\ContentInterface;

/**
 * Content Collection Block
 *
 * @ORM\Entity
 */
class ContentCollectionBlock extends Block
{
    /**
     * @var ContentInterface[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\BlockContent", mappedBy="block", cascade={"persist"})
     */
    protected $collection;

    public function __construct()
    {
        parent::__construct();

        $this->collection = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'content_collection';
    }

    /**
     * @param  BlockContent $content
     * @return $this
     */
    public function addContent(BlockContent $content)
    {
        $this->collection[] = $content;

        return $this;
    }

    /**
     * @param BlockContent $content
     */
    public function removeContent(BlockContent $content)
    {
        $this->collection->removeElement($content);
    }

    /**
     * @param  Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return ArrayCollection|ContentInterface[]
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
