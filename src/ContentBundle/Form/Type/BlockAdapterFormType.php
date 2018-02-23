<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlockAdapterFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['block_service']) || !$options['block_service'] instanceof BlockServiceInterface) {
            throw new InvalidOptionsException('The block_service option should be an instance of \Opifer\ContentBundle\Block\Service\BlockServiceInterface');
        }

        $options['block_service']->buildManageForm($builder, $options);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('block_service');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FormType::class;
    }
}
