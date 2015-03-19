<?php

namespace Opifer\ContentBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\EavBundle\ValueProvider\AbstractValueProvider;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;

class ContentValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'contentpicker', [
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
        return 'Content picker';
    }
}
