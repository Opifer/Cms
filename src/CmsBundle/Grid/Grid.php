<?php

namespace Opifer\CmsBundle\Grid;

use APY\DataGridBundle\Grid\Grid as BaseGrid;

/**
 * Grid
 *
 * Overrides the APYDataGridBundle Grid to show/hide columns
 */
class Grid extends BaseGrid
{
    const REQUEST_QUERY_COLUMNS = '_columns';

    /**
     * {@inheritdoc}
     */
    protected function processRequestData()
    {
        $this->processColumns($this->getFromRequest(self::REQUEST_QUERY_COLUMNS));

        parent::processRequestData();
    }

    /**
     * Process columns
     *
     * @param array $columnIds
     *
     * @return $this
     */
    protected function processColumns($columnIds)
    {
        if (count((array) $columnIds)) {
            $this->set(self::REQUEST_QUERY_COLUMNS, $columnIds);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function processSessionData()
    {
        parent::processSessionData();

        // Columns
        if (($columnIds = $this->get(self::REQUEST_QUERY_COLUMNS)) !== null) {
            if (count((array) $columnIds)) {
                foreach ($this->columns as $column) {
                    if (!in_array($column->getId(), $columnIds)) {
                        $column->setVisible(false);
                    } else {
                        $column->setVisible(true);
                    }
                }
            }
        }
    }
}
