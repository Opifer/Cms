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
}
