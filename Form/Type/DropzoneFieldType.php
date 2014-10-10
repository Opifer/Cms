<?php

namespace Opifer\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * To be renamed
 */
class DropzoneFieldType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'attr' => ['value' => '__value__']
            ])
            ->add('tags', 'tags', [
                'tagfield'     => [],
                'autocomplete' => 'dynamic', // default
                'attr'         => ['help_text' => 'Typ the tagnames and separate them with a comma. E.g. Red, Green, Blue']
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'dropzone_field';
    }
}
