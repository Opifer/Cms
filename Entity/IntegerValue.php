<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Integer Value
 *
 * @ORM\Entity
 */
class IntegerValue extends Value
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
