<?php

namespace Opifer\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\MediaBundle\Form\Transformer\CollectionToStringTransformer;
use Opifer\MediaBundle\Model\MediaManagerInterface;

/**
 * The dropzone form type
 */
class DropzoneType extends AbstractType
{
    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    /**
     * @param MediaManagerInterface $mm
     */
    public function __construct(MediaManagerInterface $mm)
    {
        $this->mediaManager = $mm;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['mapped']) {
            $prototype = $builder->create('__id__', new DropzoneFieldType(), array_replace([
                'label' => '__id__label__'
            ], []));

            $builder->setAttribute('prototype', $prototype->getForm());
        }

        $builder->addViewTransformer(new CollectionToStringTransformer($this->mediaManager));
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'path'        => $options['path'],
            'prototype'   => (!$options['mapped']) ? $form->getConfig()->getAttribute('prototype')->createView($view) : null,
            'form_action' => $options['form_action'],
            'mapped'      => $options['mapped']
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false
        ]);

        $resolver->setRequired([
            'path',
            'form_action'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'dropzone';
    }
}
