<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Not mapped yet.
 */
class DateValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', DatePickerType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'format'   => 'yyyy-MM-dd'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\DateValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Date';
    }
}
