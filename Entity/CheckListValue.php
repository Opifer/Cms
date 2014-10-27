<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * CheckListValue
 *
 * @ORM\Entity
 */
class CheckListValue extends Value implements ValueInterface
{
}
