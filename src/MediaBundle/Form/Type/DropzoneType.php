<?php

namespace Opifer\MediaBundle\Form\Type;

use Opifer\MediaBundle\Form\Transformer\CollectionToStringTransformer;
use Opifer\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * The dropzone form type.
 */
class DropzoneType extends AbstractType
{
    /** @var MediaManagerInterface */
    protected $mediaManager;

    /**
     * @param MediaManagerInterface $mm
     */
    public function __construct(MediaManagerInterface $mm)
    {
        $this->mediaManager = $mm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['mapped']) {
            $prototype = $builder->create('__id__', 'opifer_media_edit', array_replace([
                'label' => '__id__label__',
            ], []));

            $builder->setAttribute('prototype', $prototype->getForm());
        }

        $builder->addViewTransformer(new CollectionToStringTransformer($this->mediaManager));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'path' => $options['path'],
            'prototype' => (!$options['mapped']) ? $form->getConfig()->getAttribute('prototype')->createView($view) : null,
            'form_action' => $options['form_action'],
            'mapped' => $options['mapped'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
        ]);

        $resolver->setRequired([
            'path',
            'form_action',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dropzone';
    }
}
