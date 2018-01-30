<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\FormBlockBundle\Entity\FileFieldBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * File Field Block Service.
 */
class FileFieldBlockService extends FormFieldBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new FileFieldBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('File Field', 'file_field');

        $tool->setIcon('file_upload')
            ->setGroup('Form')
            ->setDescription('Include a file upload field');

        return $tool;
    }
}
