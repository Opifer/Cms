<?php

namespace Opifer\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RequirementType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parameter', TextType::class, [
                'label' =>'opifer_redirect.form.parameter.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.parameter.help_text',
                ]
            ])
            ->add('value', TextType::class, [
                'label' =>'opifer_redirect.form.value.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.value.help_text',
                ]
            ])
        ;
    }
}
