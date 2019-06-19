<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TemplateType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.name',
                'attr'  => [
                    'placeholder' => 'template.form.name.placeholder',
                    'help_text'   => 'template.form.name.help_text',
                ]
            ])
            ->add('display_name', TextType::class, [
                'label' => 'form.display_name',
                'attr'  => [
                    'placeholder' => 'template.form.display_name.placeholder',
                    'help_text'   => 'template.form.display_name.help_text',
                ]
            ])
            ->add('view', TextType::class, [
                'label' => 'form.view',
                'attr'  => [
                    'placeholder' => 'template.form.view.placeholder',
                    'help_text'   => 'template.form.view.help_text',
                ]
            ])
        ;
    }
}
