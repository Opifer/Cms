<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Opifer\EavBundle\Model\Schema as BaseSchema;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Schema
 *
 * @ORM\Table(name="schema")
 * @ORM\Entity(repositoryClass="Opifer\EavBundle\Repository\SchemaRepository")
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
     * @ORM\Column(name="object_class", type="string", length=128)
     */
    protected $objectClass;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Attribute", mappedBy="schema", cascade={"all"}, orphanRemoval=true)
     */
    protected $attributes;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Attribute", mappedBy="allowedSchemas")
     **/
    protected $allowedInAttributes;
}
