<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StylesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($array) {
                return $array;
            },
            function ($array) {
                // Always convert the values to a simple array, since in some cases the values might be passed with custom
                // keys, which is then converted to an object on json_encoding.
                return array_values($array);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'label.styling',
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'attr' => ['help_text' => 'help.html_styles', 'tag' => 'styles'],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
