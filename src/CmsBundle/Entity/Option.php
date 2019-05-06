<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\EavBundle\Model\AttributeInterface;
use Opifer\EavBundle\Model\Option as BaseOption;

/**
 * Option
 *
 * @JMS\ExclusionPolicy("all")
 */
class Option extends BaseOption
{
    /**
     * @var string
     *
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var string
     *
     * @JMS\Expose
     */
    protected $displayName;

    /**
     * @var int
     *
     * @JMS\Expose
     */
    protected $sort;

    /**
     * @var AttributeInterface
     *
     * @JMS\Expose
     */
    protected $attribute;

    /**
     * @var string
     *
     * @JMS\Expose
     */
    protected $description;
}
