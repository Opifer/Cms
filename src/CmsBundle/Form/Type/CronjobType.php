<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CronjobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('command', TextType::class, [
                'attr' => [
                    'placeholder' => 'vendor:project:command',
                ],
            ])
            ->add('expression', TextType::class, [
                'attr' => [
                    'placeholder' => '* * * * *',
                ],
            ])
            ->add('priority', NumberType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_cms_cronjob';
    }
}
