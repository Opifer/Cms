<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\ButtonBlock;
use Opifer\FormBlockBundle\Entity\FormFieldBlock;
use Opifer\FormBlockBundle\Entity\ChoiceFieldBlock;
use Opifer\FormBlockBundle\Form\Type\FormFieldValidationType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ModalBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\FormBundle\Model\FormManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Opifer\ExpressionEngine\DoctrineExpressionEngine;
use Opifer\ExpressionEngine\Form\Type\ExpressionEngineType;
use Opifer\ExpressionEngine\Prototype\AndXPrototype;
use Opifer\ExpressionEngine\Prototype\Choice;
use Opifer\ExpressionEngine\Prototype\OrXPrototype;
use Opifer\ExpressionEngine\Prototype\Prototype;
use Opifer\ExpressionEngine\Prototype\PrototypeCollection;
use Opifer\ExpressionEngine\Prototype\SelectPrototype;
use Opifer\ExpressionEngine\Prototype\TextPrototype;
use Opifer\ExpressionEngine\Prototype\EventPrototype;

/**
 * Modal Block Service
 */
class ModalBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::Class, ['inherit_data' => true])
                ->add('name', TextType::class, ['label' => 'label.name', 'attr' => ['help_text' => 'help.block_name']])
                ->add('title', TextType::class, ['attr' => ['help_text' => 'help.title']])
                ->add('header', CKEditorType::class, ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('value', CKEditorType::class, ['label' => 'label.body', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('footer', CKEditorType::class, ['label' => 'label.footer', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        );

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
            ->add('backdrop', CheckboxType::class, [
                'label' => 'label.modal_backdrop',
                'attr' => [
                    'align_with_widget'     => true,
                    'help_text'             => 'help_text.modal_backdrop',
                ],
            ]);

        if (isset($this->config['styles']) && count($this->config['styles'])) {
            $builder->get('styles')->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ]);
        }

        if (isset($this->config['template']) && count($this->config['template'])) {
            $builder->get('styles')->add('template', ChoiceType::class, [
                'label'       => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr'        => ['help_text' => 'help.block_template'],
                'choices'     => $this->config['templates'],
                'required'    => false,
            ]);
        }
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
        return new ModalBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($this->getName(), 'modal');

        $tool
            ->setIcon('web')
            ->setGroup(Tool::GROUP_CONTENT)
            ->setDescription('Modal window in popup style');

        return $tool;
    }
}
