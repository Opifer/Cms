<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Interface BlockServiceInterface
 *
 * @Widget;
 *
 * @package Opifer\ContentBundle\Block
 */
interface BlockServiceInterface
{

    /**
     * @param BlockInterface $block
     *
     * @return mixed
     */
    public function getName(BlockInterface $block = null);

    /**
     * @return mixed
     */
    public function getView(BlockInterface $block);

    /**
     * @return mixed
     */
    public function execute(BlockInterface $block, Response $response = null);

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options);

}