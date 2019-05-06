<?php

namespace Opifer\ContentBundle\ValueProvider;

use Opifer\ContentBundle\Entity\ContentValue;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ContentValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', ContentPickerType::class, [
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return ContentValue::class;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Content item';
    }
}
