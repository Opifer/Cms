<?php

namespace Opifer\ContentBundle\ValueProvider;

use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ContentValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', ContentPickerType::class, [
            'label'    => false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\ContentBundle\Entity\ContentValue';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Content item';
    }
}
