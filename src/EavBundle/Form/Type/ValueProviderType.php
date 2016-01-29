<?php

namespace Opifer\EavBundle\Form\Type;

use Opifer\EavBundle\ValueProvider\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Value provider type
 *
 * Gives the option to choose one of the available values
 */
class ValueProviderType extends AbstractType
{
    /** @var Pool */
    protected $providerPool;

    /**
     * Constructor
     *
     * @param Pool $providerPool
     */
    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];
        foreach ($this->providerPool->getValues() as $alias => $value) {
            $choices[$alias] = $value->getLabel();
        }

        $resolver->setDefaults([
            'label'    => 'Type of value',
            'choices'  => $choices,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'value_provider';
    }
}
