<?php

namespace Opifer\EavBundle\Form\Type;

use Opifer\EavBundle\Form\EventListener\ValuesSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValuesType extends AbstractType
{
    protected $valuesSubscriber;

    /**
     * Constructor
     *
     * @param ValuesSubscriber $valuesSubscriber
     */
    public function __construct(ValuesSubscriber $valuesSubscriber)
    {
        $this->valuesSubscriber = $valuesSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->valuesSubscriber);
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'fields' => []
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'values_collection';
    }
}
