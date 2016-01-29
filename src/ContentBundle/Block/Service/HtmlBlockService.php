<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\HtmlBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * HTML Block Service
 */
class HtmlBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('value', CKEditorType::class, ['label' => 'label.rich_text', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        )->add(
            $builder->create('properties', FormType::class)
                ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
                ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new HtmlBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('Content', 'OpiferContentBundle:HtmlBlock');

        $tool->setIcon('subject')
            ->setDescription('Rich content editable through WYSIWYG editor.');

        return $tool;
    }
}
