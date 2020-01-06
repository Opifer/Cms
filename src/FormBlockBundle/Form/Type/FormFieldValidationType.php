<?php

namespace Opifer\FormBlockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class FormFieldValidationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Greater than / Equals' => 'gte',
                    'Lower than / Equals' => 'lte',
                ],
            ])
            ->add('value', TextType::class)
            ->add('message', TextAreaType::class)
        ;

        $builder->addModelTransformer(new CallbackTransformer(
            function($original) {
                return $original;
            },
            function($submitted) {
                if ($submitted) {
                    switch ($submitted['type']) {
                        case 'gte':
                        case 'lte':
                            $submitted['value'] = (int) $submitted['value'];
                            break;
                        default:
                            // Do nothing
                            break;
                    }
                }

                return $submitted;
            }
        ));
    }
}
