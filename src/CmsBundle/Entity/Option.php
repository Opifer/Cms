<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Model\Option as BaseOption;

/**
 * Option
 */
class Option extends BaseOption
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var int
     */
    protected $sort;
}
