<?php

namespace Opifer\EavBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Opifer\EavBundle\Validator\Constraints as OpiferAssert;

/**
 * Template
 *
 * @ORM\MappedSuperclass
 */
class Template implements TemplateInterface
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
     * @var string
     *
     * @ORM\Column(name="displayName", type="string", length=255)
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
     * @ORM\Column(name="object_class", type="string", length=128)
     *
     * @Assert\NotBlank()
     * @OpiferAssert\ClassExists()
     */
    protected $objectClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Opifer\EavBundle\Model\AttributeInterface", mappedBy="template", cascade={"all"}, orphanRemoval=true)
     *
     * @Assert\Valid()
     */
    protected $attributes;

    /**
     * @var string
     *
     * @ORM\Column(name="presentation", type="text", nullable=true)
     */
    protected $presentation;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
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
     * Set objectClass
     *
     * @param  string $objectClass
     *
     * @return Template
     */
    public function setObjectClass($objectClass)
    {
        $this->objectClass = $objectClass;

        return $this;
    }


    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass()
    {
        return $this->objectClass;
    }


    /**
     * Set name
     *
     * @param  string $name
     *
     * @return Template
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
     * @return Template
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
     * Add attributes
     *
     * @param  AttributeInterface $attributes
     *
     * @return Template
     */
    public function addAttribute(AttributeInterface $attributes)
    {
        $this->attributes[] = $attributes;

        return $this;
    }


    /**
     * Remove attributes
     *
     * @param AttributeInterface $attributes
     */
    public function removeAttribute(AttributeInterface $attributes)
    {
        $this->attributes->removeElement($attributes);
    }


    /**
     * Get attributes
     *
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * Get an attribute by its name
     *
     * @param string $name
     *
     * @return AttributeInterface|false
     */
    public function getAttribute($name)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() == $name) {
                return $attribute;
            }
        }

        return false;
    }


    /**
     * Set presentation
     *
     * @param  string $presentation
     *
     * @return Template
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }


    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }
}
