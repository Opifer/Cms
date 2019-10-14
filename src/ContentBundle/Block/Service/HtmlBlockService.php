<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\HtmlBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false]);

        if (isset($this->config['templates'])) {
            $builder->get('properties')
                ->add('template', ChoiceType::class, [
                    'label'       => 'label.template',
                    'placeholder' => 'placeholder.choice_optional',
                    'attr'        => ['help_text' => 'help.block_template', 'tag' => 'styles'],
                    'choices'     => $this->config['templates'],
                    'required'    => false,
                ]);
        }
        // Default panel
        $builder->add(
            $builder->get('default')
                ->add('value', CKEditorType::class, [
                    'label' => 'label.rich_text',
                    'attr' => [
                        'label_col' => 12,
                        'widget_col' => 12,
                        'help_text' => 'help.html_rich_text'
                    ],
                    'required' => false
                ])
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
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Rich text', 'html');

        $tool->setIcon('text_fields')
            ->setDescription('Rich content editable through WYSIWYG editor.');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Rich content editable through WYSIWYG editor.';
    }
}
