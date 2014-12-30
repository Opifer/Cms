<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Not mapped yet.
 */
class DatetimeValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'genemu_jquerydate', [
            'label' => false,
            'widget' => 'single_text'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\DateTimeValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'DateTime';
    }
}
