<?php

namespace Opifer\EavBundle\Model;

interface ValueSetInterface
{
    /**
     * Get schema.
     *
     * @return SchemaInterface
     */
    public function getSchema();

    /**
     * Set schema.
     *
     * @param SchemaInterface
     */
    public function setSchema(SchemaInterface $schema = null);

    /**
     * Get values.
     *
     * @return ValueInterface[]
     */
    public function getValues();

    /**
     * Get value by attribute name.
     *
     * @param string $name
     *
     * @throws \BadMethodCallException If no value was found
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Get value by attribute name or return null if the attribute is not found
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getOrNull($name);
}
