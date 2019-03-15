<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\StringValue;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StringValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return StringValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Textfield';
    }
}
