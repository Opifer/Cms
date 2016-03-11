<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;
use Opifer\ContentBundle\Block\VisitorInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * CompositeBlock
 *
 * @ORM\Entity
 */
abstract class CompositeBlock extends Block implements BlockContainerInterface, \IteratorAggregate
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->children = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Add child
     *
     * @param BlockInterface $block
     *
     * @return BlockInterface
     */
    public function addChild(BlockInterface $block)
    {
        $this->children[] = $block;

        return $this;
    }

    /**
     * Remove child
     *
     * @param BlockInterface $block
     */
    public function removeChild(BlockInterface $block)
    {
        $this->children->removeElement($block);
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return ($this->getChildren() && $this->getChildren()->count() > 0) ? true : false;
    }

//
//    public function accept(Visitor $visitor)
//    {
//
//    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->getChildren();
    }


    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return $this->getIterator()->first();
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return $this->getIterator()->last();
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->getIterator()->key();
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->getIterator()->current();
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return $this->getIterator()->next();
    }


    /**
     * @param VisitorInterface $visitor
     */
    public function accept(VisitorInterface $visitor)
    {
        foreach ($this->getChildren() as $child) {
            $child->accept($visitor);
        }

        $visitor->visit($this);
    }
}