<?php

namespace Opifer\EavBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Option.
 *
 * @ORM\MappedSuperclass
 *
 * @JMS\ExclusionPolicy("all")
 */
class Option implements OptionInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var AttributeInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\EavBundle\Model\AttributeInterface", inversedBy="options", cascade={"persist"})
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    protected $attribute;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     *
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     *
     * @JMS\Expose
     */
    protected $displayName;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     *
     * @JMS\Expose
     */
    protected $sort;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @JMS\Expose
     */
    protected $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Opifer\EavBundle\Entity\Value", mappedBy="options", cascade={"all"}, orphanRemoval=true)
     */
    protected $values;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
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
     * @return Option
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
     * Set attribute.
     *
     * @param AttributeInterface $attribute
     *
     * @return Option
     */
    public function setAttribute(AttributeInterface $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute.
     *
     * @return AttributeInterface
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     *
     * @return Option
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
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return Option
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
     * Add value.
     *
     * @param ValueInterface $value
     *
     * @return Option
     */
    public function addValue(ValueInterface $value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Remove value.
     *
     * @param ValueInterface $value
     */
    public function removeValue(ValueInterface $value)
    {
        $this->values->removeElement($value);
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
