<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\Value;
use Opifer\ContentBundle\Model\ContentInterface;

/**
 * Multiple Content Value
 *
 * @ORM\Entity
 */
class MultiContentValue extends Value implements \IteratorAggregate, \Countable
{
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Opifer\ContentBundle\Model\ContentInterface")
     * @ORM\JoinTable(name="value_content",
     *      joinColumns={@ORM\JoinColumn(name="value_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="content_id", referencedColumnName="id")}
     * )
     */
    private $content;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->content = new ArrayCollection();
    }

    /**
     * Get the value
     *
     * Overrides the parent getValue method
     *
     * @return ArrayCollection
     */
    public function getValue()
    {
        return $this->content;
    }

    /**
     * Makes it possible to loop over this entity.
     *
     * @return ArrayCollection
     */
    public function getIterator()
    {
        return $this->content;
    }

    /**
     * Add content
     *
     * @param  ContentInterface $content
     * @return Value
     */
    public function addContent(ContentInterface $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param ContentInterface $content
     */
    public function removeContent(ContentInterface $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Set Contents
     *
     * @param ArrayCollection $content
     */
    public function setContent(ArrayCollection $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return ArrayCollection
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (count($this->content) < 1) ? true : false;
    }

    /**
     * Count the amount of mapped content items
     *
     * @return int
     */
    public function count()
    {
        return count($this->content);
    }

    /**
     * Get an array of content ids
     *
     * @return array
     */
    public function getIds()
    {
        $array = [];

        foreach ($this->content as $item) {
            $array[] = $item->getId();
        }

        return $array;
    }
}
