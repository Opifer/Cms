<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\LayoutBlock;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Class ColumnBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ColumnBlockService extends LayoutBlockService implements BlockServiceInterface
{

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        if ($block instanceof BlockInterface) {
            switch ($block->getName()) {
                case 'one_column':
                    return 'One column';
                    break;
                case 'two_column':
                    return 'Two columns';
                    break;
            }
        }

        return 'Layout Column';
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'column';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        $block = new LayoutBlock;
        $block->setType('one_column');

        return $block;
    }
}