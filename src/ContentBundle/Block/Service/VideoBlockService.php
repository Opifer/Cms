<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\VideoBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Video Block Service
 */
class VideoBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('title', TextType::class, [
                'label' => 'label.title',
                'required' => false,
            ])
            ->add('value', CKEditorType::class, [
                'label' => 'label.caption',
                'required' => false,
            ])
            ->add('media', MediaPickerType::class, [
                'required'  => false,
                'multiple' => false,
                'attr' => array('label_col' => 12, 'widget_col' => 12),
            ])
        ;

        $builder->get('properties')
            ->add('width', TextType::class, [
                'label' => 'label.width',
                'required' => false
            ])
            ->add('height', TextType::class, [
                'label' => 'label.height',
                'required' => false
            ])
            ->add('autoplay', ChoiceType::class, [
                'choices' => [
                    false =>'No',
                    true => 'Yes',
                ],
                'attr' => [
                    'help_text' => 'help.autoplay'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('loop', ChoiceType::class, [
                'choices' => [
                    false =>'No',
                    true => 'Yes',
                ],
                'attr' => [
                    'help_text' => 'help.loop'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new VideoBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Video', 'video');

        $tool->setIcon('movie')
            ->setDescription('Shows a HTML5 or Youtube videoplayer');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'This shows a Youtube videoplayer';
    }
}
