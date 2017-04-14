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
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('header', CKEditorType::class, [
                'label' => 'label.header',
                'attr' => ['label_col' => 12, 'widget_col' => 12],
                'required' => false
            ])
            ->add('value', CKEditorType::class, [
                'label' => 'label.body',
                'attr' => [
                    'label_col' => 12,
                    'widget_col' => 12
                ],
                'required' => false
            ])
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
                'constraints' => [
                    new NotBlank(),
                ],
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
                'attr'        => [
                    'help_text' => 'Pick a preset',
                    'tag' => 'styles'
                ],
                'choices'     => $this->config['presets'],
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
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
            ->add('id', TextType::class, [
                'attr' => [
                    'help_text' => 'help.html_id'
                ],
                'required' => false
            ])
            ->add('extra_classes', TextType::class, [
                'attr' => [
                    'help_text' => 'help.extra_classes'
                ],
                'required' => false,
            ])
            ->add('content',  ContentPickerType::class, [
                'as_object' => false,
                'label' => 'label.content',
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
