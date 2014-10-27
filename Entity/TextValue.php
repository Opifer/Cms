<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * TextValue
 *
 * @ORM\Entity
 */
class TextValue extends Value implements ValueInterface
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
