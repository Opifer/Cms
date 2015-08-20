<?php

namespace Opifer\ContentBundle\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class RecursiveBlockIterator
 *
 * @package Opifer\ContentBundle\Designer
 */
class RecursiveBlockIterator implements \RecursiveIterator
{
    /**
     * @var ArrayCollection | BlockInterface[]
     */
    private $_data;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function current()
    {
        return $this->_data->current();
    }

    public function next()
    {
        $this->_data->next();
    }

    public function key()
    {
        return $this->_data->key();
    }

    public function valid()
    {
        return $this->_data->current() instanceof BlockInterface;
    }

    public function rewind()
    {
        $this->_data->first();
    }

    public function hasChildren()
    {
        return ( !$this->_data->current()->getChildren()->isEmpty());
    }

    public function getChildren()
    {
        return new RecursiveBlockIterator($this->_data->current()->getChildren());
    }
}