<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* DateTimeValue
*
* @ORM\Entity
*/
class DateTimeValue extends Value
{

    /**
    * Turn value into string for form field value purposes
    *
    * @return string
    */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return date("Y-m-d", $this->value);
    }

    /**
     * Set value
     *
     * @param string $value
     * 
     * @return DateTimeValue
     */
    public function setValue($value)
    {
        $this->value = strtotime($value);

        return $this;
    }

    /**
     * Get raw value
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->value;
    }
}
