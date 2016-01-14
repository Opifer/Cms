<?php

namespace Opifer\CmsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Form\Type\ContentDetailsType as BaseContentDetailsType;

/**
 * Class ContentDetailsType
 *
 * @package Opifer\CmsBundle\Form\Type
 */
class ContentDetailsType extends BaseContentDetailsType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);

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
    }
}
