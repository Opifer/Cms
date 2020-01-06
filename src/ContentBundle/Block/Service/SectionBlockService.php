<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\SectionBlock;
use Opifer\ContentBundle\Form\Type\BoxModelType;
use Opifer\ContentBundle\Form\Type\StylesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Section Block Service
 */
class SectionBlockService extends AbstractBlockService implements BlockServiceInterface, LayoutBlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('header', CKEditorType::class, [
                'label' => 'label.header',
                'attr' => [
                    'label_col' => 12,
                    'widget_col' => 12,
                    'help_text' => 'help.section_header',
                ],
                'required' => false
            ])
            ->add('footer', CKEditorType::class, [
                'label' => 'label.footer',
                'attr' => [
                    'label_col' => 12,
                    'widget_col' => 12,
                    'help_text' => 'help.section_footer',
                ],
                'required' => false
            ])
        ;

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false])
            ->add('styles', StylesType::class, [
                'choices'  => $this->config['styles'],
            ])
            ->add('padding', BoxModelType::class, [
                'type' => 'padding',
                'attr' => [
                    'help_text' => 'Spacing inside',
                    'tag' => 'styles',
                ],
                'required' => false
            ])
            ->add('margin', BoxModelType::class, [
                'type' => 'margin',
                'attr' => [
                    'help_text' => 'Spacing outside',
                    'tag' => 'styles',
                ],
                'required' => false
            ])
            ->add('container_size', ChoiceType::class, [
                'label' => 'label.container_sizing',
                'choices' => [
                    'label.container_fluid' => 'fluid',
                    'label.container_fixed' => '',
                    'label.container_smooth' => 'smooth',
                ],
                'attr' => [
                    'help_text' => 'help.container_sizing',
                    'tag' => 'styles',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new SectionBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($this->getName(), 'section');

        $tool
            ->setIcon('crop_din')
            ->setGroup(Tool::GROUP_LAYOUT)
            ->setDescription('Section element to hold columns or content in');

        return $tool;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlaceholders(BlockInterface $block = null)
    {
        return [0 => 'children'];
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Section element to hold columns or content in';
    }
}
