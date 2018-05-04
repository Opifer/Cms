<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Not mapped yet.
 */
class DatetimeValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', DateTimePickerType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label'    => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\DateTimeValue';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'DateTime';
    }
}
