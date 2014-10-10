<?php

namespace Opifer\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Media Type
 *
 * This form type builds the create form for various kinds of media
 */
class MediaType extends AbstractType
{
    /**
     * Build the form
     *
     * @param Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                       $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['data'] || null === $options['data']->getId()) {
            $options['provider']->buildCreateForm($builder, $options);
        } else {
            $options['provider']->buildEditForm($builder, $options);
        }

        $builder->add('provider', 'hidden', ['data' => $options['provider']->getName()]);
    }

    /**
     * Set default options
     *
     * Sets defaults, but also enables custom options that should be able to get
     * passed to the buildForm method from the controller. (e.g. 'provider')
     *
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'provider' => null,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'mediatype';
    }
}
