<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Opifer\EavBundle\Eav\AttributeInterface;

use Opifer\CrudBundle\Annotation as CRUD;
use Opifer\Component\Validation\Constraints as OpiferAssert;

/**
 * AttributeType
 *
 * @ORM\Table(name="attribute")
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="attributes")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     **/
    protected $template;

    /**
     * @var string
     *
     * @CRUD\Form(editable=true, type="value_provider")
     * @ORM\Column(name="value_type", type="string", length=128)
     */
    protected $valueType;

    /**
     * @var string
     *
     * @CRUD\Form(editable=true, type="slug")
     * @ORM\Column(name="name", type="string", length=128)
     * @OpiferAssert\Slug
     */
    protected $name;

    /**
     * @var string
     *
     * @CRUD\Form(editable=true)
     * @ORM\Column(name="display_name", type="string", length=255)
     */
    protected $displayName;

    /**
     * @var integer
     *
     * @CRUD\Form(editable=true)
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

    /**
     * @CRUD\Form(editable=true)
     * @ORM\OneToMany(targetEntity="Option", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     */
    protected $options;

    /**
     * @JMS\Type("array<Opifer\EavBundle\Entity\Value>")
     * @ORM\OneToMany(targetEntity="Value", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     */
    protected $values;

    /**
     * Do not remove, for diff purposes on array of objects
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getId() .' '. $this->getDisplayName();
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
     * @param  string    $name
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
     * @param  string    $displayName
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
     * Set template
     *
     * @param  \Opifer\EavBundle\Entity\Template $template
     * @return Attribute
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
     * Constructor
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    /**
     * Set sort
     *
     * @param  integer   $sort
     * @return Attribute
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
     * @param  \Opifer\EavBundle\Entity\Value $values
     * @return Attribute
     */
    public function addValue(Value $values)
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Remove values
     *
     * @param \Opifer\EavBundle\Entity\Value $values
     */
    public function removeValue(Value $values)
    {
        $this->values->removeElement($values);
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
     * @param  \Opifer\EavBundle\Entity\Option $options
     * @return Attribute
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
    }

    /**
     * Set valueType
     *
     * @param  string    $valueType
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
