<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * PointerBlock is used as a relation to any shared block
 * and will not contain any data itself.
 *
 * @ORM\Entity
 */
class PointerBlock extends CompositeBlock
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
     * Overrides the CompositeBlock's getChildren method to pass the reference as this block's children
     *
     * {@inheritdoc}
     */
    public function getChildren()
    {
        $children = new ArrayCollection();
        if ($this->reference) {
            $children->add($this->reference);
        }

        return $children;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'pointer';
    }
}
