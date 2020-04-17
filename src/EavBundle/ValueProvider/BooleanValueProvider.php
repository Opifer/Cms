<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\BooleanValue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
        $builder->add('value', CheckboxType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return BooleanValue::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Checkbox';
    }
}
