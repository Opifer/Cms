<?php

namespace Opifer\CmsBundle\Form\Type;

use Opifer\ContentBundle\Form\Type\ContentType as BaseContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentType
 *
 * @package Opifer\CmsBundle\Form\Type
 */
class ContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('indexable', CheckboxType::class, [
                'label' => 'content.form.indexable.label',
                'attr' => [
                    'align_with_widget' => true,
                    'class' => 'before-form-section',
                    'help_text' => 'content.form.indexable.help_text',
                ],
            ])
            ->add('searchable', CheckboxType::class, [
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

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BaseContentType::class;
    }
}
