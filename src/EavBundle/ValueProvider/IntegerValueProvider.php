<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\IntegerValue;
use Opifer\EavBundle\Form\Transformer\AngularAttributeTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class IntegerValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AngularAttributeTransformer();
        $attr = $transformer->transform($options);

        $builder->add('value', IntegerType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label'    => false,
            'attr'     => $attr
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return IntegerValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Number';
    }
}
