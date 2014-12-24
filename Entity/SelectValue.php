<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Select Value
 *
 * @ORM\Entity
 */
class SelectValue extends Value
{
    /**
     * Turn value into a simple array
     *
     * @return array
     */
    public function getOptionsAsArray()
    {
        $array = [];

        foreach ($this->options as $option) {
            $array[$option->getId()] = $option->getName();
        }

        return $array;
    }

    /**
     * Turn the options into a json string
     *
     * @return string
     */
    public function getOptionsAsJson()
    {
        return json_encode($this->getOptionsAsArray(), true);
    }

    /**
     * Get the option ids as a string
     *
     * @return string
     */
    public function getIdsAsString()
    {
        $array = [];

        foreach ($this->options as $option) {
            $array[] = $option->getId();
        }

        return implode(',', $array);
    }

    /**
     * Get names of options
     *
     * @return array
     */
    public function getNames()
    {
        $array = [];

        foreach ($this->options as $option) {
            $array[] = $option->getName();
        }

        return $array;
    }

    /**
     * Get the value
     *
     * @return array
     */
    public function getValue()
    {
        return $this->options;
    }
}
