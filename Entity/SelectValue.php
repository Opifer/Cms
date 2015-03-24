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
        
        if(isset($options[0]) && $options[0]) {
            return $options[0]->getName();
        }
        
        return null;
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
