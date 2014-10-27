<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Opifer\EavBundle\Eav\ValueInterface;
use Opifer\EavBundle\Nesting\Nestable;

/**
 * Nested Value
 *
 * @ORM\Entity
 */
class NestedValue extends Value implements ValueInterface, \IteratorAggregate
{
    /**
     * @var <Content>
     *
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Nesting\Nestable", mappedBy="nestedIn")
     * @ORM\OrderBy({"nestedSort" = "ASC"})
     */
    protected $nested;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->nested = new ArrayCollection();
    }

    /**
     * Turn value into string for form field value purposes
     *
     * @return string
     */
    public function __toString()
    {
        $string = '';
        foreach ($this->nested as $nested) {
            $string .= $nested->getId();

            if ($this->nested->last() != $nested) {
                $string .= ',';
            }
        }

        return $string;
    }

    /**
     * Get the value
     *
     * Overrides the parent getValue method
     *
     * @return ArrayCollection
     */
    public function getValue()
    {
        return $this->nested;
    }

    /**
     * Add nested
     *
     * @param \Opifer\EavBundle\Nesting\Nestable $nested
     *
     * @return Value
     */
    public function addNested(Nestable $nested)
    {
        $this->nested[] = $nested;

        return $this;
    }

    /**
     * Remove nested
     *
     * @param \Opifer\EavBundle\Nesting\Nestable $nested
     */
    public function removeNested(Nestable $nested)
    {
        $this->nested->removeElement($nested);
    }

    /**
     * Set nested
     *
     * @param ArrayCollection $nested
     */
    public function setNested(ArrayCollection $nested)
    {
        $this->nested = $nested;

        return $this;
    }

    /**
     * Get nested
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNested()
    {
        return $this->nested;
    }

    /**
     * Makes it possible to loop over this entity.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $values = explode(',', trim($this->value));

        return $this->getNested();
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (count($this->nested) < 1) ? true : false;
    }
}
