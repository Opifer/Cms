<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Opifer\CrudBundle\Annotation as CRUD;
use Opifer\EavBundle\Model\Attribute as BaseAttribute;

/**
 * Attribute
 *
 * @ORM\Entity()
 * @ORM\Table(name="attribute")
 * @JMS\ExclusionPolicy("none")
 */
class Attribute extends BaseAttribute
{
    /**
     * @var string
     *
     * @ORM\Column(name="value_type", type="string", length=128)
     *
     * @CRUD\Form(editable=true, type="value_provider")
     */
    protected $valueType;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255)
     *
     * @CRUD\Form(editable=true)
     * @Assert\NotBlank()
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     *
     * @CRUD\Form(editable=true, type="text")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     *
     * @CRUD\Form(editable=true)
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer")
     *
     * @Assert\NotBlank()
     * @CRUD\Form(editable=true)
     */
    protected $sort = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Option", mappedBy="attribute", cascade={"all"}, orphanRemoval=true)
     *
     * @CRUD\Form(editable=true)
     */
    protected $options;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Template", inversedBy="allowedInAttributes", cascade={"persist"})
     * @ORM\JoinTable(name="attribute_allowed_template")
     **/
    protected $allowedTemplates;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=true)
     */
    protected $parameters;

    /**
     * Set $parameters
     *
     * @param  array    $parameters
     * @return MenuItem
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get $parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
