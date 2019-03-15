<?php

namespace Opifer\ContentBundle\Block;

/**
 * Certain clients needs to know if they're working with adapter or
 * real block entities.
 */
interface BlockAdapterInterface
{

    /**
     * Return the Doctrine managed entity
     *
     * @return object
     */
    public function getEntity();

    /**
     * Set the Doctrine managed entity
     *
     * @param object $entity
     *
     * @return mixed
     */
    public function setEntity($entity);
}
