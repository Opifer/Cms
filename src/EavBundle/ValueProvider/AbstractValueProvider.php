<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractValueProvider
{
    protected $enabled = true;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildParametersForm(FormBuilderInterface $builder, array $options = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $class = explode('\\', get_class($this));
        $class = end($class);

        return strtolower(str_replace('ValueProvider', '', $class));
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return ucfirst($this->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
