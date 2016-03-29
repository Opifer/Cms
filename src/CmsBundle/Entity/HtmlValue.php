<?php

namespace Opifer\CmsBundle\Entity;

use Opifer\EavBundle\Entity\Value;

/**
 * HtmlValue.
 */
class HtmlValue extends Value
{
    /**
     * Turn value into string for form field value purposes.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
