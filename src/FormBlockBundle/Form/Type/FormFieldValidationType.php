<?php

namespace Opifer\FormBlockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FormFieldValidationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Required' => 'required',
                    'Greater than / Equals' => 'gte',
                    'Lower than / Equals' => 'lte',
                    'Regex' => 'regex',
                ],
                'choices_as_values' => true
            ])
            ->add('value', TextType::class)
            ->add('message', TextType::class)
        ;
    }
}
