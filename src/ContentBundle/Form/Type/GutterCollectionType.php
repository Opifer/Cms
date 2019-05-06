<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GutterCollectionType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class GutterCollectionType extends AbstractType
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
                $sizeKeys = range(0, $columnCount-1);
                $data = array_fill_keys(['xs','sm','md','lg'], array_fill_keys($sizeKeys, 30));
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
            'entry_type' => ColumnGutterType::class,
            'column_count' => 1,
            'entry_options' => ['label' => false],
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'gutter_collection';
    }
}
