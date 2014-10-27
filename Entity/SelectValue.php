<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * Select Value
 *
 * @ORM\Entity
 */
class SelectValue extends Value implements ValueInterface
{
    // SelectValue specific functionality
}
