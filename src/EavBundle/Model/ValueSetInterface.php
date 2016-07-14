<?php

namespace Opifer\EavBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface ValueSetInterface
{
    /**
     * Get schema
     *
     * @return SchemaInterface
     */
    public function getSchema();

    /**
     * Set schema
     *
     * @param SchemaInterface
     */
    public function setSchema(SchemaInterface $schema = null);

    /**
     * Get values
     *
     * @return ValueInterface[]
     */
    public function getValues();
}
