<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CrudBundle\Annotation as CRUD;
use Opifer\EavBundle\Model\Template as BaseTemplate;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Template
 *
 * @ORM\Table(name="template")
 * @ORM\Entity(repositoryClass="Opifer\EavBundle\Repository\TemplateRepository")
 * @CRUD\Form(type="eav_template")
 */
class Template extends BaseTemplate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @CRUD\Grid(listable=true)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="displayName", type="string", length=255)
     *
     * @CRUD\Grid(listable=true)
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, unique=true)
     *
     * @CRUD\Grid(listable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="object_class", type="string", length=128)
     *
     * @CRUD\Grid(listable=true)
     */
    protected $objectClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="template", cascade={"all"}, orphanRemoval=true)
     *
     * @CRUD\Grid(listable=true)
     */
    protected $attributes;

    /**
     * @var string
     *
     * @ORM\Column(name="presentation", type="text", nullable=true)
     */
    protected $presentation;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Attribute", mappedBy="allowedTemplates")
     **/
    protected $allowedInAttributes;

    /**
     * @var string
     *
     * @ORM\Column(name="post_notify", type="string", length=255, nullable=true)
     *
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     *
     */
    protected $postNotify;

    /**
     * Set postNotify
     *
     * @param string $postNotify
     *
     * @return Template
     */
    public function setPostNotify($postNotify)
    {
        $this->postNotify = $postNotify;

        return $this;
    }

    /**
     * Get postNotify
     *
     * @return string
     */
    public function getPostNotify()
    {
        return $this->postNotify;
    }
}
