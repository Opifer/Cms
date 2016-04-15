<?php

namespace Opifer\MailingListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * MailingListType Type.
 */
class MailingListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'label.name',
                'attr' => [
                    'placeholder' => 'placeholder.name',
                ],
            ])
            ->add('displayName', TextType::class, [
                'required' => true,
                'label' => 'label.display_name',
                'attr' => [
                    'placeholder' => 'placeholder.display_name',
                ],
            ])
            ->add('provider', ChoiceType::class, [
                'required' => true,
                'label' => 'label.provider',
                'choices' => [
                    'Mailplus' => 'mailplus',
                ],
                'choices_as_values' => true,
                'attr' => [
                    'placeholder' => 'placeholder.provider',
                ],
            ])
        ;
    }
}
