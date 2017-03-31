<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Span Collection Type.
 */
class SpanCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $columnCount = $event->getForm()->getConfig()->getOption('column_count');

            $data = $event->getData();

            if (empty($data) || !count($data)) {
                $sizeKeys = range(0, $columnCount - 1);
                $data = array_fill_keys(['xs', 'sm', 'md', 'lg', 'xl'], array_fill_keys($sizeKeys, 12 / $columnCount));
                $event->setData($data);
            }
        });

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'type' => new ColumnEnumType(),
            'column_count' => 1,
            'choices' => [0, 1, 2, 3, 4, 5],
            'options' => ['label' => false],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'span_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }
}
