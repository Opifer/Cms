<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\CmsBundle\Form\Type\MultipleChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StylesType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'label.styling',
            'required' => false,
            'attr' => ['help_text' => 'help.html_styles', 'tag' => 'styles'],
            'simple_array' => true,
        ]);
    }

    public function getParent()
    {
        return MultipleChoiceType::class;
    }
}
