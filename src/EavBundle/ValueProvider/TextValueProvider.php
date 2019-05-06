<?php

namespace Opifer\EavBundle\ValueProvider;

use Opifer\EavBundle\Entity\TextValue;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class TextValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextareaType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label'    => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return TextValue::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Textarea';
    }
}
