<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

class ContentValueProvider extends AbstractValueProvider implements ValueProviderInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'contentpicker', [
            'label'    => false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        return 'Opifer\EavBundle\Entity\ContentValue';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Content picker';
    }
}
