<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\Model\ValueInterface;

/**
 * MenuGroupValue.
 *
 * @ORM\Entity()
 */
class MenuGroupValue extends Value implements ValueInterface
{
    /**
     * Turn value into string for.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
