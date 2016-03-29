<?php

namespace Opifer\CmsBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 */
class Config
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[a-z_]+$/",
     *     message="The name can be only lowercase words, separated by '_' characters"
     * )
     */
    protected $name;

    /**
     * @var object
     */
    protected $value;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
     * Get id.
     *
     * @return int
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the actual value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * setCurrentValue alias.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        return $this->value = $value;
    }

    /**
     * Get updated.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Config
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
}
