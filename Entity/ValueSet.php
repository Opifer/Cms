<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\ValueSet as BaseValueSet;

/**
 * ValueSet
 *
 * @ORM\Table(name="valueset")
 * @ORM\MappedSuperclass()
 * @JMS\ExclusionPolicy("all")
 */
class ValueSet extends BaseValueSet
{
}
