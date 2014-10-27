<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * HtmlValue
 *
 * @ORM\Entity
 */
class HtmlValue extends Value implements ValueInterface
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
