<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Form\Transformer\AngularAttributeTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class QueryValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AngularAttributeTransformer();
        $attr = $transformer->transform($options);

        $builder->add('value', 'ruleeditor', [
            'label'    => false,
            'provider' => 'content',
            'attr'     => $attr
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\QueryValue';
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Content conditions';
    }
}
