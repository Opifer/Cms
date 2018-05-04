<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Tab Form Type
 */
class TabType extends AbstractType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'label.tab_label',
                'attr'  => [
                    'placeholder' => 'placeholder.tab_label',
                ]
            ])
            ->add('parameters', TextType::class, [
                'label' => 'label.tab_parameters',
                'attr'  => [
                    'placeholder' => 'placeholder.tab_parameters',
                ]
            ])
            ->add('sort', HiddenType::class, [
                'label' => 'label.tab_sort',
                'attr'  => [
                    'class'       => 'sort-input',
                    'placeholder' => 'placeholder.tab_sort',
                ]
            ])
            ->add('key', HiddenType::class, [
                'label' => 'label.tab_key',
                'attr'  => [
                    'placeholder' => 'placeholder.tab_key',
                ]
            ])
        ;
    }
}
