<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StringValue
 *
 * @ORM\Entity
 */
class StringValue extends Value
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
