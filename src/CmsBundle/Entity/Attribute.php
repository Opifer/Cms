<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\Attribute as BaseAttribute;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attribute.
 *
 * @JMS\ExclusionPolicy("none")
 */
class Attribute extends BaseAttribute
{
    /**
     * @var string
     */
    protected $valueType;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $displayName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     */
    protected $sort = 0;

    /**
     * @var ArrayCollection
     */
    protected $options;

    /**
     * @var ArrayCollection
     **/
    protected $allowedSchemas;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Set $parameters.
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
     * Get $parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
