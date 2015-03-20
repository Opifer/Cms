<?php

namespace Opifer\EavBundle\Entity;

/**
 * Option value
 */
abstract class OptionValue extends Value
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
     * Get an array of option ids
     *
     * @return array
     */
    public function getIds()
    {
        $array = [];

        foreach ($this->options as $option) {
            $array[] = $option->getId();
        }

        return $array;
    }

    /**
     * Get the option ids as a string
     *
     * @return string
     */
    public function getIdsAsString()
    {
        $array = $this->getIds();

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
