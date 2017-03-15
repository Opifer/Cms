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

        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        if (isset($this->config['templates'])) {
            $builder->get('styles')
                ->add('template', ChoiceType::class, [
                    'label'       => 'label.template',
                    'placeholder' => 'placeholder.choice_optional',
                    'attr'        => ['help_text' => 'help.block_template'],
                    'choices'     => $this->config['templates'],
                    'required'    => false,
                ]);
        }
        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('value', CKEditorType::class, ['label' => 'label.rich_text', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        )->add(
            $propertiesForm
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
}
