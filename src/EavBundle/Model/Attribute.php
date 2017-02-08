<?php

namespace Opifer\EavBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attribute.
 *
 * @ORM\MappedSuperclass
 *
 * @JMS\ExclusionPolicy("none")
 */
class Attribute implements AttributeInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Schema
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\SchemaInterface", inversedBy="attributes")
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     **/
    protected $schema;

    /**
     * @var string
     *
     * @ORM\Column(name="value_type", type="string", length=128)
     */
    protected $valueType;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     *
     * @Assert\Regex("/^[a-z0-9_]+$/")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     *
     * @Assert\NotBlank()
     */
    protected $sort = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Model\OptionInterface", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     */
    protected $options;

    /**
     * @var ArrayCollection
     *
     * @JMS\Type("array<Opifer\EavBundle\Entity\Value>")
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Entity\Value", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     */
    protected $values;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Opifer\EavBundle\Model\SchemaInterface", inversedBy="allowedInAttributes", cascade={"persist"})
     * @ORM\JoinTable(name="attribute_allowed_schema")
     **/
    protected $allowedSchemas;

    /**
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean")
     */
    protected $required = false;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=true)
     */
    protected $parameters;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->allowedSchemas = new ArrayCollection();
    }

    /**
     * Do not remove, for diff purposes on array of objects.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getId().' '.$this->getDisplayName();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return Attribute
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set Description.
     *
     * @param string $description
     *
     * @return Attribute
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set schema.
     *
     * @param SchemaInterface $schema
     *
     * @return Attribute
     */
    public function setSchema(SchemaInterface $schema = null)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Get schema.
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Add values.
     *
     * @param ValueInterface $values
     *
     * @return Attribute
     */
    public function addValue(ValueInterface $values)
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Remove values.
     *
     * @param ValueInterface $values
     */
    public function removeValue(ValueInterface $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values.
     *
     * @return ArrayCollection
     */
    public function getValues()
    {
        return $this->values;
    }

    public function hasOptions()
    {
        return (count($this->getOptions()) > 0);
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add options.
     *
     * @param OptionInterface $options
     *
     * @return Attribute
     */
    public function addOption(OptionInterface $options)
    {
        $this->options[] = $options;

        return $this;
    }

    /**
     * Remove options.
     *
     * @param OptionInterface $options
     */
    public function removeOption(OptionInterface $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get an option by its name.
     *
     * @param string $name
     *
     * @return OptionInterface|false
     */
    public function getOptionByName($name)
    {
        foreach ($this->options as $option) {
            if ($option->getName() == $name) {
                return $option;
            }
        }

        return false;
    }

    /**
     * Set valueType.
     *
     * @param string $valueType
     *
     * @return Attribute
     */
    public function setValueType($valueType)
    {
        $this->valueType = $valueType;

        return $this;
    }

    /**
     * Get valueType.
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * Build a new value.
     *
     * @return Opifer\EavBundle\Model\ValueInterface
     */
    public function buildNewValue()
    {
        $className = $this->valueType;

        return new $className();
    }

    /**
     * Add allowed schema.
     *
     * @param SchemaInterface $schema
     *
     * @return AttributeInterface
     */
    public function addAllowedSchema(SchemaInterface $schema)
    {
        $exists = false;
        foreach ($this->allowedSchemas as $allowedSchema) {
            if ($allowedSchema->getId() == $schema->getId()) {
                $exists = true;
            }
        }

        if (!$exists) {
            $this->allowedSchemas[] = $schema;
        }

        return $this;
    }

    /**
     * Remove allowed schema.
     *
     * @param SchemaInterface $schema
     */
    public function removeAllowedSchema(SchemaInterface $schema)
    {
        $this->allowedSchemas->removeElement($schema);
    }

    /**
     * @return ArrayCollection
     */
    public function getAllowedSchemas()
    {
        return $this->allowedSchemas;
    }

    /**
     * @param ArrayCollection $allowedSchemas
     *
     * @return Attribute
     */
    public function setAllowedSchemas(ArrayCollection $allowedSchemas)
    {
        $this->allowedSchemas = $allowedSchemas;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters
     *
     * @return Attribute
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
