<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * Integer Value
 *
 * @ORM\Entity
 */
class IntegerValue extends Value implements ValueInterface
{

}
