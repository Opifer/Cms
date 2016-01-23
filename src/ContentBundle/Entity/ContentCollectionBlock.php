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
class ContentCollectionBlock extends Block implements \IteratorAggregate, \Countable
{
    /**
     * @var BlockContent[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\ContentBundle\Entity\BlockContent", mappedBy="block", cascade={"persist"})
     */
    protected $blockContentCollection;

    public function __construct()
    {
        parent::__construct();

        $this->blockContentCollection = new ArrayCollection();
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
    public function addBlockContent(BlockContent $content)
    {
        $this->blockContentCollection[] = $content;

        return $this;
    }

    /**
     * @param BlockContent $content
     */
    public function removeBlockContent(BlockContent $content)
    {
        $this->blockContentCollection->removeElement($content);
    }

    /**
     * @param  Collection $collection
     * @return $this
     */
    public function setBlockContentCollection($collection)
    {
        $this->blockContentCollection = $collection;

        return $this;
    }

    /**
     * Get the collection of BlockContent items
     *
     * @return ArrayCollection|BlockContent[]
     */
    public function getBlockContentCollection()
    {
        return $this->blockContentCollection;
    }

    /**
     * Get the collection of actual content items
     *
     * @return ContentInterface[]|ArrayCollection
     */
    public function getCollection()
    {
        $collection = new ArrayCollection();
        foreach ($this->blockContentCollection as $blockContent) {
            $collection->add($blockContent->getContent());
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->blockContentCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->getCollection();
    }
}
