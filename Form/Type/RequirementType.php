<?php

namespace Opifer\RedirectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequirementType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parameter', 'text', [
                'label' =>'opifer_redirect.form.parameter.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.parameter.help_text',
                ]
            ])
            ->add('value', 'text', [
                'label' =>'opifer_redirect.form.value.label',
                'attr' => [
                    'help_text' => 'opifer_redirect.form.value.help_text',
                ]
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_requirement';
    }
}
