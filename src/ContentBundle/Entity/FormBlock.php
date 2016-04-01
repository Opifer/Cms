<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Opifer\ContentBundle\Entity\Block;

/**
 * FormBlock
 *
 * @ORM\Entity
 */
class FormBlock extends Block
{
    /**
     * @var Form
     *
     * @Revisions\Revised
     * @ORM\ManyToOne(targetEntity="Opifer\CmsBundle\Entity\Form", fetch="EAGER")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $form;

    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'form';
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return NavigationBlock
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get shared
     *
     * @return boolean
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Set form
     *
     * @param \Opifer\CmsBundle\Entity\Form $form
     *
     * @return FormBlock
     */
    public function setForm(\Opifer\CmsBundle\Entity\Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \Opifer\CmsBundle\Entity\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Add inheritedBy
     *
     * @param \Opifer\ContentBundle\Entity\Block $inheritedBy
     *
     * @return FormBlock
     */
    public function addInheritedBy(\Opifer\ContentBundle\Entity\Block $inheritedBy)
    {
        $this->inheritedBy[] = $inheritedBy;

        return $this;
    }

    /**
     * Remove inheritedBy
     *
     * @param \Opifer\ContentBundle\Entity\Block $inheritedBy
     */
    public function removeInheritedBy(\Opifer\ContentBundle\Entity\Block $inheritedBy)
    {
        $this->inheritedBy->removeElement($inheritedBy);
    }

    /**
     * Get inheritedBy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInheritedBy()
    {
        return $this->inheritedBy;
    }

    /**
     * Remove owning
     *
     * @param \Opifer\ContentBundle\Entity\Block $owning
     */
    public function removeOwning(\Opifer\ContentBundle\Entity\Block $owning)
    {
        $this->owning->removeElement($owning);
    }

    /**
     * Add child
     *
     * @param \Opifer\ContentBundle\Entity\Block $child
     *
     * @return FormBlock
     */
    public function addChild(\Opifer\ContentBundle\Entity\Block $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Opifer\ContentBundle\Entity\Block $child
     */
    public function removeChild(\Opifer\ContentBundle\Entity\Block $child)
    {
        $this->children->removeElement($child);
    }
}
