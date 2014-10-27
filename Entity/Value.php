<?php

namespace Opifer\EavBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use JMS\Serializer\Annotation as JMS;

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
 * @see  Opifer\EavBundle\Listener\DiscriminatorListener
 *
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\TranslationEntity(class="Opifer\EavBundle\Entity\Translation\ValueTranslation")
 */
class Value
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
     * @var  Attribute
     *
     * @ORM\ManyToOne(targetEntity="Attribute", inversedBy="values", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    protected $attribute;

    /**
     * @var  ValueSet
     *
     * @ORM\ManyToOne(targetEntity="ValueSet", inversedBy="values", cascade={"persist"})
     * @ORM\JoinColumn(name="valueset_id", referencedColumnName="id")
     */
    protected $valueSet;

    /**
     * @var string
     *
     * @JMS\Expose
     * @ORM\Column(name="value", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    protected $value;

    /**
     * @var  ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Option", inversedBy="values", cascade={"detach"})
     * @ORM\JoinTable(name="value_options")
     */
    protected $options;

    /**
     * @var  string
     *
     * @Gedmo\Locale
     */
    protected $locale;

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
     * @param \Opifer\EavBundle\Entity\Attribute $attribute
     *
     * @return ValueInterface
     */
    public function setAttribute(Attribute $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \Opifer\EavBundle\Entity\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set valueSet
     *
     * @param \Opifer\EavBundle\Entity\ValueSet $valueSet
     *
     * @return ValueInterface
     */
    public function setValueSet(ValueSet $valueSet = null)
    {
        $this->valueSet = $valueSet;

        return $this;
    }

    /**
     * Get valueSet
     *
     * @return \Opifer\EavBundle\Entity\ValueSet
     */
    public function getValueSet()
    {
        return $this->valueSet;
    }

    /**
     * Set translatable locale
     *
     * @param string $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Add options
     *
     * @param  \Opifer\EavBundle\Entity\Option $options
     * @return Value
     */
    public function addOption(Option $options)
    {
        $this->options[] = $options;

        return $this;
    }

    /**
     * Remove options
     *
     * @param \Opifer\EavBundle\Entity\Option $options
     */
    public function removeOption(Option $options)
    {
        $this->options->removeElement($options);

        return $this;
    }

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        if (is_array($options)) {
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
     * @return \Doctrine\Common\Collections\Collection
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
