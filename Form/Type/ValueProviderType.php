<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\EavBundle\ValueProvider\Pool;

/**
 * ValueClass form field
 *
 * Gives the option to choose one of the available values
 */
class ValueProviderType extends AbstractType
{
    protected $providerPool;

    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = array();
        foreach ($this->providerPool->getValues() as $alias => $value) {
            $choices[$alias] = $value->getLabel();
        }

        $resolver->setDefaults([
            'label'    => 'Type of value',
            'choices'  => $choices
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'value_provider';
    }
}
