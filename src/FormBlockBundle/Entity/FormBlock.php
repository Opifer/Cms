<?php

namespace Opifer\FormBlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\CmsBundle\Entity\Form;
use Opifer\ContentBundle\Entity\CompositeBlock;
use Opifer\Revisions\Mapping\Annotation as Revisions;

/**
 * FormBlock
 *
 * @ORM\Entity
 */
class FormBlock extends CompositeBlock
{
    /**
     * @var string
     *
     * @Revisions\Revised
     * @ORM\Column(type="text", nullable=true)
     */
    protected $name;

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
