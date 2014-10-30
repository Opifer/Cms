<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HtmlValue
 *
 * @ORM\Entity
 */
class HtmlValue extends Value
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
