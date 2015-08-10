<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\SchemaInterface;

/**
 * FormValue
 *
 * Has a relation to a Schema, which defines the formfields in the form.
 *
 * @ORM\Entity
 */
class FormValue extends Value
{
    /**
     * @var SchemaInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\SchemaInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    protected $schema;

    /**
     * Set schema
     *
     * @param  SchemaInterface $schema
     * @return Value
     */
    public function setSchema(SchemaInterface $schema = null)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Get schema
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (is_null($this->schema)) ? true : false;
    }
}
