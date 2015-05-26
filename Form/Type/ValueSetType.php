<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $builder->add('namedvalues', 'values_collection');
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->valueSetClass,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'opifer_valueset';
    }
}
