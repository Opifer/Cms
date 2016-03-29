<?php

namespace Opifer\CmsBundle\Entity;

use Opifer\EavBundle\Entity\Value;
use Opifer\FormBundle\Model\FormInterface;

/**
 * FormValue
 */
class FormValue extends Value
{
    /**
     * @var FormInterface
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
