<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\LayoutBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Entity\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LayoutBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class LayoutBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $view = 'OpiferContentBundle:Block:layout.html.twig';

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getView(), array(
            'block_service'  => $this,
            'block'          => $block,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getManageView(), array(
            'block_service'  => $this,
            'block'          => $block,
            'block_view'     => $this->getView(),
            'manage_type'    => $this->getManageFormTypeName(),
        ), $response);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Layout';
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', 'form', ['virtual' => true])
                    ->add('columnCount', 'number')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new LayoutBlock;
    }
}