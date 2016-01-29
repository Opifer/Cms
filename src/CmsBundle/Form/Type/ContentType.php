<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\ContentBundle\Form\Type\ContentType as BaseContentType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentType
 *
 * @package Opifer\CmsBundle\Form\Type
 */
class ContentType extends BaseContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('indexable', 'checkbox', [
                'label' => 'content.form.indexable.label',
                'attr' => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'content.form.indexable.help_text',
                ],
            ])
            ->add('searchable', 'checkbox', [
                'label' => 'content.form.searchable.label',
                'attr' => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'content.form.searchable.help_text',
                ],
            ])
        ;

        parent::buildForm($builder, $options);
    }
}
