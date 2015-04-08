<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Select Value
 *
 * @ORM\Entity
 */
class SelectValue extends OptionValue
{
    /**
     * Get the selected value
     *
     * @return string
     */
    public function getValue()
    {
        $options = parent::getValue();

        if (count($options)) {
            return $options[0]->getName();
        }
        
        return '';
    }

    /**
     * Change the selected value into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
