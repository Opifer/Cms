<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StringValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label' => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\StringValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Textfield';
    }
}
