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

        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('title', TextType::class, [
                    'label' => 'label.title',
                ])
                ->add('value', CKEditorType::class, [
                    'label' => 'label.caption',
                ])
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                    'attr' => array('label_col' => 12, 'widget_col' => 12),
                ])
        );

        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('width', TextType::class, [
                    'label' => 'label.width',
                ])
                ->add('height', TextType::class, [
                    'label' => 'label.height',
                ])
        );
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
}
