<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentEditorType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class ContentEditorType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'content_editor';
    }
}
