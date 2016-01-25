<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class BlockAdapterFormType
 *
 * @package Opifer\Form\Type
 */
class BlockAdapterFormType implements FormTypeInterface
{
    /** @var BlockServiceInterface */
    protected $blockService;

    /**
     * @param BlockServiceInterface $blockService
     */
    public function __construct(BlockServiceInterface $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->blockService->buildManageForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //$this->blockService->buildManageView($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        //$this->blockService->finishManage($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        if (!$resolver instanceof OptionsResolver) {
            throw new \InvalidArgumentException(sprintf('Custom resolver "%s" must extend "Symfony\Component\OptionsResolver\OptionsResolver".', get_class($resolver)));
        }

        $this->configureOptions($resolver);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $this->blockService->configureManageOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->blockService->getManageFormTypeName();
    }
}
