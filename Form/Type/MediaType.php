<?php

namespace Opifer\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * @param FormBuilderInterface $builder
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
     * @deprecated
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * Set default options
     *
     * Sets defaults, but also enables custom options that should be able to get
     * passed to the buildForm method from the controller. (e.g. 'provider')
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'provider' => null,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_media_media';
    }
}
