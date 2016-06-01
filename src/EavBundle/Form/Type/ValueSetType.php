<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValueSetType extends AbstractType
{
    /** @var string */
    protected $valueSetClass;

    /**
     * Constructor
     *
     * @param string $valueSetClass
     */
    public function __construct($valueSetClass)
    {
        $this->valueSetClass = $valueSetClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('namedvalues', ValuesType::class, [ 'fields' => $options['fields'], 'label' => false]);
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->valueSetClass,
            'fields'     => []
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_valueset';
    }
}
