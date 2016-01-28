<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * PointerBlock is used as a relation to any shared block
 * and will not contain any data itself.
 *
 * @ORM\Entity
 */
class PointerBlock extends Block
{
    /**
     * @var BlockInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\ContentBundle\Entity\Block", fetch="EAGER")
     * @ORM\JoinColumn(name="reference_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reference;

    /**
     * @return BlockInterface
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param BlockInterface $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'pointer';
    }
}
