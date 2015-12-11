<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CronjobType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('command', 'text', [
                'attr' => [
                    'placeholder' => 'vendor:project:command'
                ]
            ])
            ->add('expression', 'text', [
                'attr' => [
                    'placeholder' => '* * * * *'
                ]
            ])
            ->add('priority', 'number')
        ;
    }

    /**
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_cms_cronjob';
    }
}
