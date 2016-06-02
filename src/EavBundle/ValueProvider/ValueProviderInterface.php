<?php

namespace Opifer\EavBundle\ValueProvider;

use Symfony\Component\Form\FormBuilderInterface;

interface ValueProviderInterface
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options);

    /**
     * Build a form to define additional data on the Attribute.
     *
     * @param FormBuilderInterface $builder
     * @param array|null           $options
     */
    public function buildParametersForm(FormBuilderInterface $builder, array $options = null);

    /**
     * Get the full entity namespace.
     *
     * @return string
     */
    public function getEntity();

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Check if the current valueprovider is enabled.
     *
     * @return bool
     */
    public function isEnabled();
}
