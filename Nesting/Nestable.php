<?php

namespace Opifer\EavBundle\Nesting;

use Opifer\EavBundle\Entity\NestedValue;

interface Nestable
{
    /**
     * Set the value the nestable entity is nested in
     *
     * @param NestedValue $value
     */
    public function setNestedIn(NestedValue $value);

    /**
     * Get the value the nestable entity is nested in
     *
     * @return NestedValue
     */
    public function getNestedIn();

    /**
     * Set nested sort
     *
     * @param integer $sort
     */
    public function setNestedSort($sort);

    /**
     * Get nested sort
     *
     * @return integer
     */
    public function getNestedSort();
}
