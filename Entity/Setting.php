<?php

namespace Opifer\CmsBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Opifer\CrudBundle\Annotation as Opifer;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="setting")
 * @ORM\MappedSuperclass()
 */
class Setting
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Opifer\Grid(listable=true)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string")
     * @Opifer\Grid(listable=true)
     * @Assert\NotBlank()
     */
    protected $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false, unique=true)
     * @Opifer\Grid(listable=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z.]+$/",
     *     message="The name can be only lowercase words, separated by '.' characters"
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=1024, nullable=true)
     * @Opifer\Grid(listable=true)
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1024)
     * @Assert\NotBlank()
     * @Opifer\Grid(listable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="min", type="string", nullable=true)
     */
    protected $min;

    /**
     * @var string
     *
     * @ORM\Column(name="max", type="string", nullable=true)
     */
    protected $max;

    /**
     * @var string
     *
     * @ORM\Column(name="choices", type="string", nullable=true)
     */
    protected $choices;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Opifer\Grid(listable=true)
     */
    protected $updatedAt;

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
     * Get id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the actual extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * setCurrentextension alias
     *
     * @param mixed $extension
     */
    public function setExtension($extension)
    {
        return $this->extension = $extension;
    }

    /**
     * Get the actual value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * setCurrentValue alias
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        return $this->value = $value;
    }

    /**
     * Set description
     *
     * @param  string   $description
     * @return Settings
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set name
     *
     * @param  string   $name
     * @return Settings
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
     * Set type
     *
     * @param  string   $type
     * @return Settings
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set min
     *
     * @param  string   $min
     * @return Settings
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param  string   $max
     * @return Settings
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return string
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set choices
     *
     * @param  string   $choices
     * @return Settings
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get choices
     *
     * @return string
     */
    public function getChoices()
    {
        return $this->choices;
    }
}
