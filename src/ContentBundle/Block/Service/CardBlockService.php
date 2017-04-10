<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CardBlock;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * Card Block Service
 */
class CardBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('header', CKEditorType::class, ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
            ->add('value', CKEditorType::class, ['label' => 'label.body', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
            ->add('media', MediaPickerType::class, [
                'required'  => false,
                'multiple' => false,
                'attr' => array('label_col' => 12, 'widget_col' => 12),
            ])
        ;
        $builder->get('properties')
            ->add('displaySize', ChoiceType::class, [
                'label' => 'label.list_display_size',
                'choices'  => [
                    null => 'Default',
                    'sm' => 'Small',
                    'md' => 'Medium',
                    'lg' => 'Large',
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'help_text' => 'help.list_display_size', 
                    'class' => 'btn-group btn-group-styling', 
                    'data-toggle' => 'buttons',
                    'tag' => 'styles'
                ],
                'label_attr' => ['class' => 'btn'],
            ])
            ->add('preset', ChoiceType::class, [
                'label'       => 'Preset',
                'attr'        => ['help_text' => 'Pick a preset', 'tag' => 'styles'],
                'choices'     => $this->config['presets'],
                'required'    => true,
            ])

            ->add('background', ChoiceType::class, [
                'required' => false,
                'label' => 'label.background_color',
                'choices' => $this->config['backgrounds'],
                'attr'  => ['tag' => 'styles']
            ])
            ->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'help_text' => 'help.html_styles', 
                    'class' => 'radio-rows',
                    'tag' => 'styles'
                ],
            ])
        ;

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
            ->add('content',  ContentPickerType::class, [
                'as_object' => false,
                'label' => 'label.content',
            ])
            ->add('imageRatio', ChoiceType::class, [
                'label' => 'label.list_image_ratio',
                'choices'  => [
                    null => 'No image',
                    '11' => '1:1',
                    '43' => '4:3',
                    '34' => '3:4 (portrait)',
                    '32' => '3:2',
                    '23' => '2:3 (portrait)',
                    '169'=> '16:9',
                    '916'=> '9:16 (portrait)',
                    'bg' => 'Background cover',
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'attr' => ['help_text' => 'help.list_image_ratio', 'class' => 'btn-group btn-group-styling btn-image-ratio', 'data-toggle' => 'buttons'],
                'label_attr' => ['class' => 'btn'],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'content';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CardBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($this->getName(), 'card');

        $tool
            ->setIcon('video_label')
            ->setGroup(Tool::GROUP_CONTENT)
            ->setDescription('Flexible content block in card style');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return mixed
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'Flexible content block in card style';
    }
}
