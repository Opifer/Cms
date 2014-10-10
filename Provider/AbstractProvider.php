<?php

namespace Opifer\MediaBundle\Provider;

use Symfony\Component\Form\FormBuilderInterface;

use Opifer\MediaBundle\Entity\Media;

abstract class AbstractProvider
{
    const DISABLED = 0;
    const ENABLED = 1;
    const HASPARENT = 2;

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
     * {@inheritDoc}
     */
    public function buildEditForm(FormBuilderInterface $builder, array $options)
    {
        // Simply uses the 'new' form by default.
        // If the 'edit' form should be different, override in the child class
        $this->newForm($builder, $options);
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
    public function prePersist(Media $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(Media $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(Media $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(Media $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(Media $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(Media $media)
    {
        // Do nothing, or override in child class
    }

    /**
     * {@inheritdoc}
     */
    public function indexView()
    {
        return 'OpiferMediaBundle:Base:single.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function newView()
    {
        return 'OpiferMediaBundle:Base:new.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function editView()
    {
        return 'OpiferMediaBundle:Base:edit.html.twig';
    }
}
