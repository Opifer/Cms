<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ColumnSpanType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class ColumnSpanType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sizes = ['xs','sm', 'md', 'lg'];

        foreach ($sizes as $size) {
            $builder->add($size, 'text', ['label' => 'label.column.'.$size]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'column_span';
    }
}
