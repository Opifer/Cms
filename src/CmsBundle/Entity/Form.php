<?php

namespace Opifer\CmsBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use JMS\Serializer\Annotation as JMS;
use Opifer\FormBundle\Model\Form as BaseForm;

/**
 * @GRID\Source(columns="id, name")
 * @JMS\ExclusionPolicy("all")
 */
class Form extends BaseForm
{
    /**
     * @var int
     *
     * @JMS\Expose
     *
     * @GRID\Column(title="Id", size="10", type="number")
     */
    protected $id;
}
