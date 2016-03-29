<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Opifer\ContentBundle\Model\ContentType as BaseContentType;

/**
 * Content.
 *
 * @JMS\ExclusionPolicy("all")
 * @GRID\Source(columns="id, name")
 */
class ContentType extends BaseContentType
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;

    /**
     * @var string
     * 
     * @JMS\Expose
     * @JMS\Groups({"detail", "list"})
     */
    protected $name;
}
