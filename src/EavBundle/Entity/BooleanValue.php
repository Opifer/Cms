<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Boolean Value
 *
 * Boolean values have to be stored as string, cause we're extending the Value.
 * Value uses a generic 'text' property to store any kinds of values.
 *
 * @ORM\Entity
 */
class BooleanValue extends Value
{
    const TRUE = '1';
    const FALSE = '0';

    protected $value = self::FALSE;

    /**
     * Turn value into string for form field value purposes
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->getValue()) ? 'true' : 'false';
    }

    /**
     * Set value
     *
     * @param boolean $value
     *
     * @return ValueInterface
     */
    public function setValue($value)
    {
        $this->value = ($value) ? self::TRUE : self::FALSE;

        return $this;
    }

    /**
     * Get the value as a boolean
     *
     * @return boolean
     */
    public function getValue()
    {
        return ($this->value == self::TRUE) ? true : false;
    }
}
