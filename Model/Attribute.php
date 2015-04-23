<?php

namespace Opifer\EavBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attribute
 *
 * @ORM\MappedSuperclass
 *
 * @JMS\ExclusionPolicy("none")
 */
class Attribute implements AttributeInterface
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
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\TemplateInterface", inversedBy="attributes")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     **/
    protected $template;

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
     * @Assert\Regex("/^[a-z-_]+$/")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     *
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     *
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer")
     *
     * @Assert\NotBlank()
     */
    protected $sort;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Model\OptionInterface", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     */
    protected $options;

    /**
     * @JMS\Type("array<Opifer\EavBundle\Entity\Value>")
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Entity\Value", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     */
    protected $values;


    /**
     * Do not remove, for diff purposes on array of objects
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getId() . ' ' . $this->getDisplayName();
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
     * Set name
     *
     * @param  string $name
     *
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set displayName
     *
     * @param  string $displayName
     *
     * @return Attribute
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }


    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }


    /**
     * Set Description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set template
     *
     * @param  TemplateInterface $template
     *
     * @return Attribute
     */
    public function setTemplate(TemplateInterface $template = null)
    {
        $this->template = $template;

        return $this;
    }


    /**
     * Get template
     *
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values  = new ArrayCollection();
        $this->options = new ArrayCollection();
    }


    /**
     * Set sort
     *
     * @param integer $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }


    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }


    /**
     * Add values
     *
     * @param  ValueInterface $values
     *
     * @return Attribute
     */
    public function addValue(ValueInterface $values)
    {
        $this->values[] = $values;

        return $this;
    }


    /**
     * Remove values
     *
     * @param ValueInterface $values
     */
    public function removeValue(ValueInterface $values)
    {
        $this->values->removeElement($values);
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
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Add options
     *
     * @param  OptionInterface $options
     *
     * @return Attribute
     */
    public function addOption(OptionInterface $options)
    {
        $this->options[] = $options;

        return $this;
    }


    /**
     * Remove options
     *
     * @param OptionInterface $options
     */
    public function removeOption(OptionInterface $options)
    {
        $this->options->removeElement($options);
    }


    /**
     * Get an option by its name
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
     * Set valueType
     *
     * @param  string $valueType
     *
     * @return Attribute
     */
    public function setValueType($valueType)
    {
        $this->valueType = $valueType;

        return $this;
    }


    /**
     * Get valueType
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }


    /**
     * Build a new value
     *
     * @return Opifer\EavBundle\Eav\ValueInterface
     */
    public function buildNewValue()
    {
        $className = $this->valueType;

        return new $className();
    }
}
