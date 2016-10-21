<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CardBlock;
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

        $builder->add(
            $builder->create('default', FormType::Class, ['inherit_data' => true])
                ->add('header', CKEditorType::class, ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('value', CKEditorType::class, ['label' => 'label.body', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                    'attr' => array('label_col' => 12, 'widget_col' => 12),
                ])
                ->add('name', TextType::class, ['attr' => ['help_text' => 'help.block_name']])
        );

        $propertiesForm = $builder->create('properties', FormType::Class)
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);


        $propertiesForm->add('styles', ChoiceType::class, [
            'label' => 'label.styling',
            'choices'  => $this->config['styles'],
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'attr' => ['help_text' => 'help.html_styles'],
        ]);

        $builder->add($propertiesForm);
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
}
