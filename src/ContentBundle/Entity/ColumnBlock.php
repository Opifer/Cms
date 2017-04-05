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
        return 'column';
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        if(!$this->columnCount){
            $this->setColumnCount(1);
        }
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