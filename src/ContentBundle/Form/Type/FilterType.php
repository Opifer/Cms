<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Filter Form Type.
 */
class FilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'name',
                    'help_text' => 'A name for internal usage. only lowercase and dots are allowed',
                ],
            ])
            ->add('displayName', TextType::class, [
                'label' => 'displayName',
                'attr' => [
                    'placeholder' => 'displayName',
                    'help_text' => 'The name shown to the user',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Textfield' => 'text',
                    'Buttons' => 'buttons',
                ],
                'choices_as_values' => true,
            ])
        ;
    }
}
