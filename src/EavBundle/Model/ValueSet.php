<?php

namespace Opifer\EavBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ValueSet
 *
 * @ORM\MappedSuperclass
 * @JMS\ExclusionPolicy("all")
 */
class ValueSet implements ValueSetInterface
{
    /**
     * @var integer
     *
     * @JMS\Expose
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var SchemaInterface
     *
     * @ORM\ManyToOne(
     *     targetEntity="Opifer\EavBundle\Model\SchemaInterface"
     * )
     * @ORM\JoinColumn(
     *     name="schema_id",
     *     referencedColumnName="id",
     *     onDelete="CASCADE"
     * )
     **/
    protected $schema;

    /**
     * @var ArrayCollection
     *
     * @JMS\Expose
     * @ORM\OneToMany(
     *     targetEntity="Opifer\EavBundle\Entity\Value",
     *     mappedBy="valueSet",
     *     cascade={"all"},
     *     fetch="EAGER",
     *     orphanRemoval=true
     * )
     */
    protected $values;

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
     * {@inheritdoc}
     *
     * @return ValueSet
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
     * Add values
     *
     * @param ValueInterface $values
     *
     * @return ValueSet
     */
    public function addValue(ValueInterface $value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Remove values
     *
     * @param ValueInterface $values
     */
    public function removeValue(ValueInterface $value)
    {
        $this->values->removeElement($value);
    }

    /**
     * Get values
     *
     * @return ArrayCollection
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
     * @param  string $order
     *
     * @return array
     */
    public function getSortedValues($order = 'asc')
    {
        $values = $this->values->toArray();
        usort($values, function ($a, $b) use ($order) {
            $left = $a->getAttribute()->getSort();
            $right = $b->getAttribute()->getSort();

            if ($order == 'desc') {
                return ($left < $right) ? 1 : -1;
            }

            return ($left > $right) ? 1 : -1;
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
    public function getNamedValues($order = 'asc')
    {
        $values = $this->getSortedValues($order);

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
     * @return ValueInterface
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
     * @return ValueInterface
     */
    public function getValueFor($name)
    {
        // $this->normalizeValues();
        return $this->__get($name);
    }

    /**
     * Get attributes
     *
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        if (!$this->getSchema()) {
            return new ArrayCollection();
        }

        return $this->getSchema()->getAttributes();
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

        throw new \BadMethodCallException(sprintf('The valueset for schema "%d" does not have a value called "%s".', $this->schema->getId(), $name));
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
