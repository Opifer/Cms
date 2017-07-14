<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Form\DataTransformer\BoxModelDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoxModelType extends AbstractType
{
    const TYPE_PADDING = 'padding';
    const TYPE_MARGIN = 'margin';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $prefix = ($options['type'] == self::TYPE_MARGIN) ? 'm' : 'p';

        $builder
            ->add('side', ChoiceType::class, [
                'choices' => [
                    'Spacing all around' => '',
                    'Spacing left and right' => 'x',
                    'Spacing top and bottom' => 'y',
                    'Spacing top only' => 't',
                    'Spacing right only' => 'r',
                    'Spacing bottom only' => 'b',
                    'Spacing left only' => 'l',
                ],
                'choices_as_values' => true,
            ])
            ->add('size', ChoiceType::class, [
                'choices' => [
                    'Default' => '',
                    'None (0)' => '0',
                    'Extra small (15px)' => '1',
                    'Small (20px)' => '2',
                    'Medium (30px)' => '3',
                    'Large (40px)' => '4',
                    'Extra large (50px)' => '5',
                ],
                'choices_as_values' => true,
            ])
            ->addModelTransformer(new BoxModelDataTransformer($prefix))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => self::TYPE_PADDING, // or margin
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'box_model';
    }
}
