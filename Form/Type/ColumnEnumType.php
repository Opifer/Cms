<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class ColumnEnumType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class ColumnEnumType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $columnCount = $form->getParent()->getConfig()->getOption('column_count');

            for ($i = 0; $i < $columnCount; $i++) {
                $form->add($i, 'number');
            }
        });

        parent::buildForm($builder, $options);


    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'column_span';
    }
}
