<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CrudBundle\Annotation as CRUD;
use Opifer\EavBundle\Model\Schema as BaseSchema;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Schema
 *
 * @ORM\Table(name="schema")
 * @ORM\Entity(repositoryClass="Opifer\EavBundle\Repository\SchemaRepository")
 * @CRUD\Form(type="eav_schema")
 */
class Schema extends BaseSchema
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="displayName", type="string", length=255, nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, unique=true, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="object_class", type="string", length=128)
     */
    protected $objectClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="schema", cascade={"all"}, orphanRemoval=true)
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
     * @ORM\ManyToMany(targetEntity="Attribute", mappedBy="allowedSchemas")
     **/
    protected $allowedInAttributes;
}
