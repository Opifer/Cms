<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\ValueSet as BaseValueSet;

/**
 * ValueSet
 *
 * @JMS\ExclusionPolicy("all")
 */
class ValueSet extends BaseValueSet
{
}
