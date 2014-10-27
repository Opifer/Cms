<?php

namespace Opifer\EavBundle\Eav;

use Opifer\EavBundle\Entity\ValueSet;

/**
 * Entity Interface.
 */
interface EntityInterface
{
    public function setValueSet(ValueSet $valueSet);

    public function getValueSet();
}
