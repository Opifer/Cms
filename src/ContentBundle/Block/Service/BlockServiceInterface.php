<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface BlockServiceInterface.
 *
 * @Widget;
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
     * @param BlockInterface $block
     *
     * @return mixed
     */
    public function getDescription(BlockInterface $block);

    /**
     * @param BlockInterface $block
     * @param Response|null  $response
     * @param array          $parameters
     *
     * @return Response
     */
    public function execute(BlockInterface $block, Response $response = null, array $parameters = []);

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options);

    /**
     * Executed before the form handles the request and officially submits the form.
     *
     * @param BlockInterface $block
     */
    public function preFormSubmit(BlockInterface $block);

    /**
     * Executed after the form is defined valid and before the block is actually persisted.
     *
     * @param FormInterface  $form
     * @param BlockInterface $block
     */
    public function postFormSubmit(FormInterface $form, BlockInterface $block);

    /**
     * Loads additional data onto the block
     *
     * @param BlockInterface $block
     * @return void
     */
    public function load(BlockInterface $block);
}
