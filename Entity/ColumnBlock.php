<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * ColumnBlock
 *
 * @ORM\Entity
 */
class ColumnBlock extends CompositeBlock implements BlockContainerInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="column_count", nullable=true)
     */
    protected $columnCount;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Block %d: %d columns", $this->id, $this->columnCount);
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        switch ($this->columnCount) {
            case 1:
                return 'column_one';
            case 2:
                return 'column_two';
            case 3:
                return 'column_three';
            case 4:
                return 'column_four';
        }

        return 'column_one';
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
     *
     * @throws \Exception
     */
    public function setColumnCount($columnCount)
    {
        $this->columnCount = $columnCount;
    }

}