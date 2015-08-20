<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Block\BlockContainerInterface;

/**
 * LayoutBlock
 *
 * @ORM\Entity
 */
class LayoutBlock extends CompositeBlock implements BlockContainerInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="column_count", nullable=true)
     */
    protected $columnCount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="value", nullable=true)
     */
    protected $wrapper;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Block %d: %s with %d columns", $this->id, $this->wrapper, $this->columnCount);
    }

    /**
     * @return string
     */
    public function getBlockType()
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
     *
     * @throws \Exception
     */
    public function setColumnCount($columnCount)
    {
        $this->columnCount = $columnCount;
    }

    /**
     * @return wrapper
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * @param wrapper $wrapper
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
    }


}