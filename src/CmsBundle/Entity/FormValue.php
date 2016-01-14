<?php

namespace Opifer\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\EavBundle\Entity\Value;
use Opifer\FormBundle\Model\FormInterface;

/**
 * FormValue.
 *
 * @ORM\Entity
 */
class FormValue extends Value
{
    /**
     * @var FormInterface
     *
     * @ORM\ManyToOne(targetEntity="Opifer\FormBundle\Model\FormInterface", cascade={"persist"})
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    protected $form;

    /**
     * Set form.
     *
     * @param FormInterface $form
     *
     * @return Value
     */
    public function setForm(FormInterface $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return (is_null($this->form)) ? true : false;
    }
}
