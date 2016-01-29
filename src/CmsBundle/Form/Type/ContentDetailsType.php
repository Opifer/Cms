<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\ContentBundle\Form\Type\ContentDetailsType as BaseContentDetailsType;
use Symfony\Component\Form\FormBuilderInterface;

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
