<?php

namespace Opifer\EavBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionType extends AbstractType
{
    /** @var string */
    protected $optionClass;

    /**
     * Constructor
     *
     * @param string $optionClass
     */
    public function __construct($optionClass)
    {
        $this->optionClass = $optionClass;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('displayName', TextType::class)
            ->add('sort', NumberType::class)
            ->add('description', TextareaType::class, [
                'label' => 'Text',
                'required' => false
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->optionClass,
            'compound'   => true,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'eav_option';
    }
}
