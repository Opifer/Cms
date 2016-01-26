<?php

namespace Opifer\EavBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Validator\Constraints as OpiferAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Schema
 *
 * @ORM\MappedSuperclass
 */
class Schema implements SchemaInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="object_class", type="string", length=128)
     *
     * @Assert\NotBlank()
     * @OpiferAssert\ClassExists()
     */
    protected $objectClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Model\AttributeInterface", mappedBy="schema", cascade={"all"}, orphanRemoval=true)
     *
     * @Assert\Valid()
     */
    protected $attributes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Opifer\EavBundle\Model\AttributeInterface", mappedBy="allowedSchemas")
     **/
    protected $allowedInAttributes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->allowedInAttributes = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set objectClass
     *
     * @param  string $objectClass
     *
     * @return Schema
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }


    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * Add attributes
     *
     * @param  AttributeInterface $attributes
     *
     * @return Schema
     */
    public function addAttribute(AttributeInterface $attributes)
    {
        $this->attributes[] = $attributes;

        return $this;
    }


    /**
     * Remove attributes
     *
     * @param AttributeInterface $attributes
     */
    public function removeAttribute(AttributeInterface $attributes)
    {
        $this->attributes->removeElement($attributes);
    }


    /**
     * Get attributes
     *
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * Get an attribute by its name
     *
     * @param string $name
     *
     * @return AttributeInterface|false
     */
    public function getAttribute($name)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() == $name) {
                return $attribute;
            }
        }

        return false;
    }

    /**
     * Add allowed in attribute
     *
     * @param  AttributeInterface $attribute
     *
     * @return  SchemaInterface
     */
    public function addAllowedInAttribute(AttributeInterface $attribute)
    {
        $this->allowedInAttributes[] = $attribute;

        return $this;
    }

    /**
     * Remove allowed schema
     *
     * @param AttributeInterface $attribute
     */
    public function removeAllowedInAttribute(AttributeInterface $attribute)
    {
        $this->allowedInAttributes->removeElement($attribute);
    }

    /**
     * Get allowed in attributes
     *
     * @return ArrayCollection
     */
    public function getAllowedInAttributes()
    {
        return $this->allowedInAttributes;
    }

    /**
     * @param ArrayCollection $allowedInAttributes
     *
     * @return Schema
     */
    public function setAllowedInAttributes(ArrayCollection $allowedInAttributes)
    {
        $this->allowedInAttributes = $allowedInAttributes;

        return $this;
    }
}
