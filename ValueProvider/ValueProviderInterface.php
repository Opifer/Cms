<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

interface ValueProviderInterface
{
    /**
     * Build form
     *
     * @param  FormBuilderInterface $builder
     * @param  array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options);

    /**
     * Get the full entity namespace
     *
     * @return string
     */
    public function getEntity();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();
}
