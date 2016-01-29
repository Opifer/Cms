<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class TextValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', TextareaType::class, [
            'required' => ($options['attribute']->getRequired()) ? true : false,
            'label'    => false
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
