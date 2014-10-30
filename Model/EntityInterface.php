<?php

namespace Opifer\EavBundle\Model;

/**
 * Entity Interface.
 */
interface EntityInterface
{
    public function setValueSet(ValueSetInterface $valueSet);

    public function getValueSet();
}
