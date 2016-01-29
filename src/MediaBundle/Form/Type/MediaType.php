<?php

namespace Opifer\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Media Type.
 *
 * This form type builds the create form for various kinds of media
 */
class MediaType extends AbstractType
{
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['data'] || null === $options['data']->getId()) {
            $options['provider']->buildCreateForm($builder, $options);
        } else {
            $options['provider']->buildEditForm($builder, $options);
        }

        $builder->add('provider', HiddenType::class, ['data' => $options['provider']->getName()]);
    }

    /**
     * Set default options.
     *
     * Sets defaults, but also enables custom options that should be able to get
     * passed to the buildForm method from the controller. (e.g. 'provider')
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'provider' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_media_media';
    }
}
