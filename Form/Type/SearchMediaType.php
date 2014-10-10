<?php

namespace Opifer\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Search Media Form
 */
class SearchMediaType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search', 'genemu_jqueryselect2_hidden', [
            'attr' => [
                'placeholder' => 'Search media'
            ],
            'configs' => [
                'multiple' => false
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        // This should be 'form' to trigger the select2 field.
        return 'form';
    }
}
