<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\Value;

/**
 * HtmlValue
 *
 * @ORM\MappedSuperclass()
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
