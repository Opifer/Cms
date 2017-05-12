<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\FormBlockBundle\Entity\FormProgressBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Form Block Service.
 */
class FormProgressBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new FormProgressBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Form Progress', 'form_progress');

        $tool->setIcon('input')
            ->setGroup('Form')
            ->setDescription('Include a text field');

        return $tool;
    }
}
