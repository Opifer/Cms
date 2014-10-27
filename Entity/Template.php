<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\CrudBundle\Annotation as CRUD;
use Doctrine\Common\Collections\ArrayCollection;

use Opifer\Component\Validation\Constraints as OpiferAssert;

/**
 * Template
 *
 * @ORM\Table(name="template")
 * @ORM\Entity(repositoryClass="Opifer\EavBundle\Repository\TemplateRepository")
 */
class Template
{
    /**
     * @var integer
     *
     * @CRUD\Grid(listable=true)
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @CRUD\Grid(listable=true)
     * @ORM\Column(name="displayName", type="string", length=255)
     */
    private $displayName;

    /**
     * @var string
     *
     * @CRUD\Grid(listable=true)
     * @CRUD\Form(type="slug")
     * @ORM\Column(name="name", type="string", length=128)
     * @OpiferAssert\Slug
     */
    private $name;

    /**
     * @var string
     *
     * @CRUD\Grid(listable=true)
     * @CRUD\Form(type="template_object_class")
     * @ORM\Column(name="object_class", type="string", length=128)
     */
    private $objectClass;

    /**
     * @var ArrayCollection
     *
     * @CRUD\Grid(listable=true)
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="template", cascade={"all"}, orphanRemoval=true)
     **/
    private $attributes;

    /**
     * @var string
     *
     * @CRUD\Form(type="presentationeditor")
     * @ORM\Column(name="presentation", type="text", nullable=true)
     */
    private $presentation;

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
     * @param  string   $objectClass
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
     * @param  string   $name
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
     * @param  string   $displayName
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
     * @param  \Opifer\EavBundle\Entity\Attribute $attributes
     * @return Template
     */
    public function addAttribute(\Opifer\EavBundle\Entity\Attribute $attributes)
    {
        $this->attributes[] = $attributes;

        return $this;
    }

    /**
     * Remove attributes
     *
     * @param \Opifer\EavBundle\Entity\Attribute $attributes
     */
    public function removeAttribute(\Opifer\EavBundle\Entity\Attribute $attributes)
    {
        $this->attributes->removeElement($attributes);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set presentation
     *
     * @param  string   $presentation
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
