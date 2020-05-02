<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\CmsBundle\Form\DataTransformer\ArrayValuesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultipleChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => true,
            'simple_array' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['simple_array']) {
            $builder->addModelTransformer(new ArrayValuesTransformer());
        }
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
