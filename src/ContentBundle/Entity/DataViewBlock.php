<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;

/**
 * DataViewBlock
 *
 * @ORM\Entity
 */
class DataViewBlock extends Block
{
    /**
     * @var DataView
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\DataView", fetch="EAGER")
     * @ORM\JoinColumn(name="data_view_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $dataView;

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'data_view';
    }

    /**
     * @return DataView
     */
    public function getDataView()
    {
        return $this->dataView;
    }

    /**
     * @param DataView $media
     *
     * @return $this
     */
    public function setDataView($dataView)
    {
        $this->dataView = $dataView;

        return $this;
    }
}
