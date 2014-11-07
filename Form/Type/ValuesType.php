<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\EavBundle\Form\EventListener\ValuesSubscriber;

class ValuesType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ValuesSubscriber());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'values_collection';
    }
}
