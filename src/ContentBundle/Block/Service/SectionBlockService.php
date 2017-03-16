<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\SectionBlock;
use Opifer\ContentBundle\Form\Type\BoxModelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
            ->add('header', CKEditorType::class, ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
            ->add('footer', CKEditorType::class, ['label' => 'label.footer', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        ;

        $builder->get('properties')
            ->add('sectionName', TextType::class, ['attr' => ['help_text' => 'help.section_name']])
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
        ;

        $builder->get('styles')
            ->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ])
            ->add('padding', BoxModelType::class, [
                'type' => 'padding',
                'attr' => [
                    'help_text' => 'Spacing inside',
                ]
            ])
            ->add('margin', BoxModelType::class, [
                'type' => 'margin',
                'attr' => [
                    'help_text' => 'Spacing outside',
                ]
            ])
            ->add('container_size', ChoiceType::class, [
                'label' => 'label.container_sizing',
                'choices' => ['fluid' => 'label.container_fluid', '' => 'label.container_fixed', 'smooth' => 'label.container_smooth'],
                'required' => true,
                'attr' => ['help_text' => 'help.container_sizing'],
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
        return new SectionBlock;
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
}
