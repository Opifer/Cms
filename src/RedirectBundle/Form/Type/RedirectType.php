<?php

namespace Opifer\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RedirectType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('origin', TextType::class, [
                'label' =>'opifer_redirect.form.origin.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.origin.help_text',
                ]
            ])
            ->add('target', TextType::class, [
                'label' =>'opifer_redirect.form.target.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.target.help_text',
                ]
            ])
            ->add('permanent', CheckboxType::class, [
                'attr' => [
                    'align_with_widget' => true,
                ]
            ])
            ->add('requirements', CollectionType::class, [
                'type' => RequirementType::class,
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
}
