<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LayoutBlock
 *
 * @ORM\Entity
 * @ORM\Table(name="block_layout")
 */
class LayoutBlock extends Block
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="column_count")
     */
    protected $columnCount = 1;

    /**
     * @return string
     */
    public function getType()
    {
        return 'layout';
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columnCount;
    }

    /**
     * @param int $columnCount
     */
    public function setColumnCount($columnCount)
    {
        if ($columnCount <= 0) {
            throw \Exception("Column count should be 1 or more, not zero.");
        }

        $this->columnCount = $columnCount;
    }
}