<?php

namespace Opifer\EavBundle\ValueProvider;

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
        $builder->add('value', 'opifer_eav_datetime_picker', [
            'format' => 'yyyy-MM-dd'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\DateValue';
    }
}
