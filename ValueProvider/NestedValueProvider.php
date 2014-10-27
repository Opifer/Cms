<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

class NestedValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // The form is built manually in the eav_value form fragment inside
        // Resources/views/forms/fields.html.twig
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\NestedValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Nested content';
    }
}
