<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ContentItemBlock;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ContentItemBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    public function buildManageForm(FormBuilderInterface $builder, array $options) : void
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('value',  ContentPickerType::class, [
                'as_object' => false,
                'required' => false,
                'label' => 'label.content',
            ]);
    }

    public function createBlock() : ContentItemBlock
    {
        return new ContentItemBlock();
    }

    public function getTool(BlockInterface $block = null) : Tool
    {
        $tool = new Tool('ContentItem', 'content_item');

        $tool->setIcon('note_add')
            ->setDescription('Include another page inside the current page');

        return $tool;
    }

    public function getDescription(BlockInterface $block = null) : string
    {
        return 'Include another page inside the current page';
    }
}
