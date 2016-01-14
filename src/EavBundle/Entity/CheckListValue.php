<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CheckListValue
 *
 * @ORM\Entity
 */
class CheckListValue extends OptionValue
{
    public function getValue()
    {
       return $this->value;
    }

}
