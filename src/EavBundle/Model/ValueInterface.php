<?php

namespace Opifer\EavBundle\Model;

/**
 * Value Interface.
 */
interface ValueInterface
{
    /**
     * Get the actual value of the Value.
     *
     * This could be anything; [string|array|object|etc.].
     * If you're using relations, make sure to specify a new property and simply
     * return that property inside the getValue method.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Check if your value is actually empty. This is needed, so we can verify if
     * the value actually has data attached, so no empty values will get stored
     * in the database.
     *
     * @return boolean
     */
    public function isEmpty();
}
