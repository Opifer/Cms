<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\CrudBundle\Annotation as CRUD;
use Opifer\EavBundle\Model\Option as BaseOption;

/**
 * Option
 *
 * @ORM\Table(name="option")
 * @ORM\Entity(repositoryClass="Opifer\EavBundle\Repository\OptionRepository")
 */
class Option extends BaseOption
{
    /**
     * @var string
     *
     * @CRUD\Form(editable=true)
     * @ORM\Column(name="name", type="string", length=128)
     */
    protected $name;

    /**
     * @var string
     *
     * @CRUD\Form(editable=true)
     * @ORM\Column(name="display_name", type="string", length=255)
     */
    protected $displayName;

    /**
     * @var integer
     *
     * @CRUD\Form(editable=true)
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;
}
