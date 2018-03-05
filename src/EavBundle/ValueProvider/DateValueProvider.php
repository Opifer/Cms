<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\DateValue;
use Opifer\EavBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Not mapped yet.
 */
class DateValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', DatePickerType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'format'   => 'yyyy-MM-dd'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return DateValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Date';
    }
}
