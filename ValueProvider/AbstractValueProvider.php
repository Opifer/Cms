<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractValueProvider
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        $class = explode('\\', get_class($this));
        $class = end($class);

        return strtolower(str_replace('ValueProvider', '', $class));
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return ucfirst($this->getName());
    }
}
