<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Form\Transformer\AngularAttributeTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class IntegerValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AngularAttributeTransformer();
        $attr = $transformer->transform($options);

        $builder->add('value', 'integer', [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label'    => false,
            'attr'     => $attr
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\IntegerValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Number';
    }
}
