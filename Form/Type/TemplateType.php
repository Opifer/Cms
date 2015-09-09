<?php

namespace Opifer\FormBundle\Form\Type;

use Opifer\EavBundle\Form\Type\TemplateType as BaseTemplateType;
use Symfony\Component\Form\FormBuilderInterface;

class TemplateType extends BaseTemplateType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('attributes', 'bootstrap_collection', [
            'allow_add'    => true,
            'allow_delete' => true,
            'type'         => $this->attributeType
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_form_template';
    }
}
