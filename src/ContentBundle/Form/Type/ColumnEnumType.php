<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Column Enum Type.
 */
class ColumnEnumType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $columnCount = $form->getParent()->getConfig()->getOption('column_count');

            for ($i = 0; $i < $columnCount; ++$i) {
                $form->add($i, ChoiceType::class, [
                    'label' => 'label.column_count',
                    'placeholder' => 'placeholder.default',
                    'choices' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                    'required' => true,
                ]);
            }
        });

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'column_span';
    }
}
