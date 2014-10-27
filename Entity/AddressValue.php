<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Opifer\CmsBundle\Entity\Address;
use Opifer\EavBundle\Eav\ValueInterface;

/**
 * Address Value
 *
 * @ORM\Entity
 */
class AddressValue extends Value implements ValueInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="Opifer\CmsBundle\Entity\Address", inversedBy="contents", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * Get the value
     *
     * overrides the parent getValue method
     *
     * @return \Opifer\CmsBundle\Entity\Address
     */
    public function getValue()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param  \Opifer\CmsBundle\Entity\Address $address
     * @return Value
     */
    public function setAddress(Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \Opifer\CmsBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return (is_null($this->value) && is_null($this->address)) ? true : false;
    }
}
