<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TemplateType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class TemplateType extends AbstractType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add the default form fields
        $builder
            ->add('name', 'text', [
                'label' => 'form.name',
                'attr'  => [
                    'placeholder' => 'template.form.name.placeholder',
                    'help_text'   => 'template.form.name.help_text',
                ]
            ])
            ->add('display_name', 'text', [
                'label' => 'form.display_name',
                'attr'  => [
                    'placeholder' => 'template.form.display_name.placeholder',
                    'help_text'   => 'template.form.display_name.help_text',
                ]
            ])
            ->add('view', 'text', [
                'label' => 'form.view',
                'attr'  => [
                    'placeholder' => 'template.form.view.placeholder',
                    'help_text'   => 'template.form.view.help_text',
                ]
            ])
//            ->add('description', 'text', [
//                'label' => 'form.description',
//                'attr'  => [
//                    'placeholder' => 'template.form.description.placeholder',
//                    'help_text'   => 'template.form.description.help_text',
//                ]
//            ])
        ;

//        $builder->add('save', 'submit', [
//            'label' => 'button.submit',
//        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_template';
    }
}
