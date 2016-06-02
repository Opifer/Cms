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
     * Get values
     *
     * @return ArrayCollection
     */
    public function getValues();
}
