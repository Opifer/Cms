<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TwitterValue
 *
 * @ORM\Entity
 */
class TwitterValue extends Value
{
    /**
     * Turn value into string for
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
