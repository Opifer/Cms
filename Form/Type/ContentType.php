<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Form\Type\ContentType as BaseContentType;

/**
 * Class ContentType
 *
 * @package Opifer\CmsBundle\Form\Type
 */
class ContentType extends BaseContentType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('indexable', 'checkbox', [
                'label' => 'content.form.indexable.label',
                'attr'  => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'content.form.indexable.help_text'
                ]
            ])
            ->add('searchable', 'checkbox', [
                'label' => 'content.form.searchable.label',
                'attr'  => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'content.form.searchable.help_text'
                ]
            ])
        ;

        parent::buildForm($builder, $options);
    }
}
