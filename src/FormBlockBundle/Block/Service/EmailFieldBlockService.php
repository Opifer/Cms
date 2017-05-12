<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\FormBlockBundle\Entity\EmailFieldBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Form Block Service.
 */
class EmailFieldBlockService extends FormFieldBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new EmailFieldBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Email Field', 'email_field');

        $tool->setIcon('input')
            ->setGroup('Form')
            ->setDescription('Include an email field');

        return $tool;
    }
}
