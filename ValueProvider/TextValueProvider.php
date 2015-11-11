<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

class TextValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'textarea', [
            'required' => (isset($options['attribute']->getParameters()['required'])) ? $options['attribute']->getParameters()['required'] : false,
            'label' => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\TextValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Textarea';
    }
}
