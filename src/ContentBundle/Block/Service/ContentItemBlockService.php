<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ContentItemBlock;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * ContentItem Block Service.
 */
class ContentItemBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('value',  ContentPickerType::class, [
                    'as_object' => false,
                    'label' => 'label.content',
                ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new ContentItemBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('ContentItem', 'content_item');

        $tool->setIcon('note_add')
            ->setDescription('Include another page inside the current page');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Include another page inside the current page';
    }
}
