<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Opifer\EavBundle\Model\Schema as BaseSchema;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Schema
 */
class Schema extends BaseSchema
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $objectClass;

    /**
     * @var ArrayCollection
     */
    protected $attributes;

    /**
     * @var ArrayCollection
     **/
    protected $allowedInAttributes;
}
