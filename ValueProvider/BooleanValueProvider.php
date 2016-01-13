<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Adds value functionality for boolean attributes to the list of available values
 */
class BooleanValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'checkbox', [
            'required' => ($options['attribute']->getRequired()) ? true : false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\BooleanValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Checkbox';
    }
}
