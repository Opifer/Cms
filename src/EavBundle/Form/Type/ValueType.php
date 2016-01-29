<?php

namespace Opifer\EavBundle\Form\Type;

use Opifer\EavBundle\Entity\Value;
use Opifer\EavBundle\ValueProvider\Pool;
use Opifer\EavBundle\ValueProvider\ValueProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValueType extends AbstractType
{
    protected $name = 'eav_value';

    /**
     * @var \Opifer\EavBundle\ValueProvider\Pool
     */
    protected $providerPool;

    /**
     * Constructor
     *
     * @param \Opifer\EavBundle\ValueProvider\Pool
     */
    public function __construct(Pool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ValueProviderInterface $provider */
        $provider = $this->providerPool->getValue($options['attribute']->getValueType());
        $provider->buildForm($builder, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'attribute',
            'entity',
            'value'
        ]);

        $resolver->setDefaults([
            'data_class' => Value::class,
            'angular'    => [],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attribute'] = $options['attribute'];
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return $this->name;
    }
}
