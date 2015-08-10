<?php

namespace Opifer\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PageManagerType
 *
 * @package Opifer\ContentBundle\Form\Type
 */
class PageManagerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'hidden')
            ->add('save', 'submit', ['label' => 'button.submit']);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'page_manager';
    }
}
