<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Opifer\EavBundle\ValueProvider\Pool;

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
        $provider = $this->providerPool->getValue($options['attribute']->getValueType());
        $provider->buildForm($builder, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'attribute',
            'entity',
            'value'
        ]);

        $resolver->setDefaults([
            'data_class' => 'Opifer\EavBundle\Entity\Value',
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
    public function getName()
    {
        return $this->name;
    }
}
