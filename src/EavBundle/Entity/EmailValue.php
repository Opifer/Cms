<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailValue
 *
 * @ORM\Entity
 */
class EmailValue extends Value
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
