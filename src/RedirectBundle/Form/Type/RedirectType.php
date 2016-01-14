<?php

namespace Opifer\RedirectBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RedirectType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('origin', 'text', [
                'label' =>'opifer_redirect.form.origin.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.origin.help_text',
                ]
            ])
            ->add('target', 'text', [
                'label' =>'opifer_redirect.form.target.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.target.help_text',
                ]
            ])
            ->add('permanent', 'checkbox', [
                'attr' => [
                    'align_with_widget' => true,
                ]
            ])
            ->add('requirements', 'collection', [
                'type' => 'opifer_requirement',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' =>'opifer_redirect.form.requirements.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.requirements.help_text',
                ]
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_redirect';
    }
}
