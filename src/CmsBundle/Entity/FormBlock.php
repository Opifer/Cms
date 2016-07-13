<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
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
     * @return string
     */
    public function getBlockType()
    {
        return 'form';
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
}
