<?php

namespace Opifer\FormBlockBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\FormBlockBundle\Entity\RichCheckItemBlock;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Rich Check Item Block Service.
 */
class RichCheckItemBlockService extends FormFieldBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('title', TextType::class)
                ->add('value', CKEditorType::class, ['label' => 'label.rich_text', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                    'attr' => array('label_col' => 12, 'widget_col' => 12),
                ])
        );

        if (isset($this->config['templates'])) {
            $builder->get('properties')->add('template', ChoiceType::class, [
                'label'       => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr'        => ['help_text' => 'help.block_template'],
                'choices'     => $this->config['templates'],
                'required'    => false,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new RichCheckItemBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Rich Check Item', 'rich_check_item');

        $tool->setIcon('art_track')
            ->setGroup('Form')
            ->setDescription('Add a rich check item');

        return $tool;
    }
}
