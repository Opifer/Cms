<?php

namespace Opifer\ContentBundle\Block;

class RecursiveBlockIterator extends \RecursiveArrayIterator implements \RecursiveIterator
{
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
