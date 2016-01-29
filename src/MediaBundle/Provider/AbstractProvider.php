<?php

namespace Opifer\MediaBundle\Provider;

use Opifer\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $class = explode('\\', get_class($this));
        $class = end($class);

        return strtolower(str_replace('Provider', '', $class));
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('reference', 'text')
            ->add('add '.$this->getLabel(), 'submit')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormBuilderInterface $builder, array $options)
    {
        // Simply uses the 'new' form by default.
        // If the 'edit' form should be different, override in the child class
        $this->buildCreateForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getThumb(MediaInterface $media)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function postLoad(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(MediaInterface $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function singleView()
    {
        return 'OpiferMediaBundle:Media:single.html.twig';
    }
}
