<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DocumentBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class DocumentBlockService extends AbstractBlockService implements BlockServiceInterface
{

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Document';
    }

    /**
     * @param BlockInterface $block
     *
     * @return string
     */
    public function getManageView(BlockInterface $block)
    {
        return $this->getView($block);
    }
}