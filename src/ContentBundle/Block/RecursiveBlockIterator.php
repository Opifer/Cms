<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class RecursiveBlockIterator
 *
 * @package Opifer\ContentBundle\Designer
 */
class RecursiveBlockIterator extends \RecursiveArrayIterator implements \RecursiveIterator
{
//    /**
//     * @var ArrayCollection | BlockInterface[]
//     */
//    private $_data;

    public function __construct($array)
    {
        if (is_object($array)) {
            $array = $array->toArray();
        }

        parent::__construct($array);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new self($this->current()->getChildren());
    }
    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return $this->current()->hasChildren();
    }
}