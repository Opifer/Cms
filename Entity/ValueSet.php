<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\Serializer\Annotation as JMS;

/**
 * ValueSet
 *
 * @ORM\Table(name="valueset")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class ValueSet
{

    /**
     * @var integer
     *
     * @JMS\Expose
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     **/
    private $template;

    /**
     * @var ArrayCollection
     *
     * @JMS\Expose
     * @ORM\OneToMany(targetEntity="Value", mappedBy="valueSet", cascade={"persist"}, fetch="EAGER")
     */
    private $values;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
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
     * Set template
     *
     * @param  \Opifer\EavBundle\Entity\Template $template
     * @return ValueSet
     */
    public function setTemplate(Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Opifer\EavBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add values
     *
     * @param \Opifer\EavBundle\Entity\Value $values
     *
     * @return ValueSet
     */
    public function addValue(Value $value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Remove values
     *
     * @param \Opifer\EavBundle\Entity\Value $values
     */
    public function removeValue(Value $value)
    {
        $this->values->removeElement($value);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Normalize values
     *
     * @return ValueSet
     */
    public function normalizeValues()
    {
        $persistedAttributes = array();
        foreach ($this->values as $value) {
            $persistedAttributes[] = $value->getAttribute();
        }

        $missingAttributes = array_diff($this->getAttributes()->toArray(), $persistedAttributes);

        foreach ($missingAttributes as $attribute) {
            $value = $attribute->buildNewValue();
            $value->setValueSet($this);
            $this->addValue($value);
            $value->setAttribute($attribute);
        }

        return $this;
    }

    /**
     * Get Sorted Values
     *
     * This method wraps all values in an array, sorted by the attribute's sort
     * property.
     *
     * We use this to render a whole set of form types, which should be displayed
     * in some order. When you want to be able to get a form field by it's name
     * and place it on a custom place, use the getNamedValues() method.
     *
     * @return array
     */
    public function getSortedValues()
    {
        $values = $this->values->toArray();
        usort($values, function ($value1, $value2) {
            return ($value1->getAttribute()->getSort() > $value2->getAttribute()->getSort()) ? 1 : -1;
        });

        return $values;
    }

    /**
     * Get Named Values
     *
     * When the automatic sort of form ValueSet form fields does not matter,
     * you can use this method to get a associative array where the keys hold
     * the attribute name.
     *
     * To render a specific form field in Twig you could do something like this:
     *
     *    {{ form_row(form.valueset.namedvalues.yourformfield) }}
     *
     * @return array
     */
    public function getNamedValues()
    {
        $values = $this->getSortedValues();

        $valueArray = [];
        foreach ($values as $value) {
            $valueArray[$value->getAttribute()->getName()] = $value;
        }

        return $valueArray;
    }

    /**
     * Checks if a certain value exists on the valueset
     *
     * Created for cleaner syntax in Twig:
     *
     *     {{ content.valueset.has('attributename') }}
     * or
     *     {{ content.valueset.has(['attributename', 'anotherattributename']) }}
     *
     * @param string|array $value
     *
     * @return boolean
     */
    public function has($value)
    {
        if (!is_string($value) && !is_array($value)) {
            throw new \InvalidArgumentException('The ValueSet\'s "has" method requires the argument to be of type string or array');
        }

        if (is_string($value)) {
            return $this->__isset($value);
        } elseif (is_array($value)) {
            foreach ($value as $name) {
                if (!$this->__isset($value)) {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * Get Value for Attribute $name
     *
     * Created for cleaner syntax in Twig
     * e.g. {{ content.valueset.get('attributename') }}
     *
     * @param string $name
     *
     * @return Value
     */
    public function get($name)
    {
        return $this->__get($name);
    }

    /**
     * Get value for a given attribute name
     *
     * @param string $name
     *
     * @return Value
     */
    public function getValueFor($name)
    {
//        $this->normalizeValues();
        return $this->__get($name);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function getAttributes()
    {
        return $this->getTemplate()->getAttributes();
    }

    /**
     * Magic getter to retrieve attribute values
     *
     * @param string $name
     *
     * @return Value
     */
    public function __get($name)
    {
        foreach ($this->values as $valueObject) {
            if ($valueObject->getAttribute()->getName() == $name) {
                return $valueObject;
            }
        }

        throw new \BadMethodCallException('The valueset for template "'. $this->template->getName() .'" does not have a value called "' . $name . '".');
    }

    /**
     * Necessary __isset() to make sure the magic __get() method works in Twig.
     *
     * Checks if a certain value exists on the valueset
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        foreach ($this->values as $valueObject) {
            if ($valueObject->getAttribute()->getName() == $name) {
                return true;
            }
        }

        return false;
    }
}
