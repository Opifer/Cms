<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePickerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
            'attr' => ['class' => 'datetimepicker']
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'datetime';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_eav_datetime_picker';
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_eav_datetime_picker';
    }
}
