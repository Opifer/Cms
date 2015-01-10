<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\AttributeInterface;
use Opifer\EavBundle\Model\ValueInterface;
use Opifer\EavBundle\Model\ValueSetInterface;
use Opifer\EavBundle\Model\OptionInterface;

/**
 * Value
 *
 * @ORM\Table(name="value")
 * @ORM\Entity(repositoryClass="Opifer\EavBundle\Repository\ValueRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 *
 * The discriminatorMap is handled dynamically by the DiscriminatorListener. It
 * retrieves the mapped classes from the ValueProvider Pool and adds them to the
 * map.
 * @see  Opifer\EavBundle\EventListener\DiscriminatorListener
 *
 * @JMS\ExclusionPolicy("all")
 */
class Value implements ValueInterface
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
     * @var Attribute
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\AttributeInterface", inversedBy="values", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    protected $attribute;

    /**
     * @var ValueSet
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\ValueSetInterface", inversedBy="values", cascade={"persist"})
     * @ORM\JoinColumn(name="valueset_id", referencedColumnName="id")
     */
    protected $valueSet;

    /**
     * @var string
     *
     * @JMS\Expose
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    protected $value;

    /**
     * @var  ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Opifer\EavBundle\Model\OptionInterface", inversedBy="values", cascade={"detach"})
     * @ORM\JoinTable(name="value_options",
     *     joinColumns={@ORM\JoinColumn(name="value_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="option_id", referencedColumnName="id")}
     * )
     */
    protected $options;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
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
     * Set value
     *
     * @param string $value
     *
     * @return ValueInterface
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * Override this method in child templates to return the desired format
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set attribute
     *
     * @param AttributeInterface $attribute
     *
     * @return ValueInterface
     */
    public function setAttribute(AttributeInterface $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return AttributeInterface
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set valueSet
     *
     * @param ValueSetInterface $valueSet
     *
     * @return ValueInterface
     */
    public function setValueSet(ValueSetInterface $valueSet = null)
    {
        $this->valueSet = $valueSet;

        return $this;
    }

    /**
     * Get valueSet
     *
     * @return ValueSetInterface
     */
    public function getValueSet()
    {
        return $this->valueSet;
    }

    /**
     * Add option
     *
     * @param  OptionInterface $option
     * @return Value
     */
    public function addOption(OptionInterface $option)
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param OptionInterface $options
     */
    public function removeOption(OptionInterface $option)
    {
        $this->options->removeElement($option);

        return $this;
    }

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            $this->options = $options->toArray();
        } else {
            foreach ($options as $option) {
                $this->addOption($option);
            }
        }

        return $this;
    }

    /**
     * Get options
     *
     * @return ArrayCollection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Check whether this value is actually just a value without any content
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (is_null($this->getValue()) && count($this->options) < 1) ? true : false;
    }
}
