<?php

namespace Opifer\CmsBundle\Grid;

use APY\DataGridBundle\Grid\Column\Column;
use APY\DataGridBundle\Grid\Row;

class BooleanColumn extends Column
{
    public function __initialize(array $params)
    {
        parent::__initialize($params);

        $this->setSafe(false);
    }

    /**
     * @param string $value
     * @param Row $row
     * @param $router
     * @return string
     */
    public function renderCell($value, $row, $router)
    {
        if ($value) {
            return '<span class="label label-success">Yes</span>';
        }
        return '<span class="label label-danger">No</span>';
    }

    public function getType()
    {
        return 'boolean';
    }
}
