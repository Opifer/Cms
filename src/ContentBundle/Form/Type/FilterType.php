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
            // TODO: Rename `name` property to `filter`
            ->add('name', FilterNameType::class, [
                'label' => 'Filter',
                'attr' => [
                    'help_text' => 'What should be filtered on?',
                ],
            ])
            ->add('displayName', TextType::class, [
                'label' => 'displayName',
                'attr' => [
                    'help_text' => 'The field label shown to the user',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Textfield' => 'text',
                    'Buttons' => 'buttons',
                ],
                'choices_as_values' => true,
                'attr' => [
                    'help_text' => 'How should we display the filter?',
                ],
            ])
        ;
    }
}
