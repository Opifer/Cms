<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckListValue
 *
 * @ORM\Entity
 */
class CheckListValue extends Value
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
            $array[] = $option->getId();
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
        return json_encode($this->getOptionsAsArray());
    }
}
