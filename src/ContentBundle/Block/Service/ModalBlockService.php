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
                ->add('title', TextType::class, ['attr' => ['help_text' => 'help.title']])
                ->add('header', CKEditorType::class, ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('value', CKEditorType::class, ['label' => 'label.body', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('footer', CKEditorType::class, ['label' => 'label.footer', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        );

        $propertiesForm = $builder->create('properties', FormType::Class)
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        if (isset($this->config['styles']) && count($this->config['styles'])) {
            $propertiesForm->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ]);
        }

        if (isset($this->config['template']) && count($this->config['template'])) {
            $propertiesForm->add('template', ChoiceType::class, [
                'label'       => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr'        => ['help_text' => 'help.block_template'],
                'choices'     => $this->config['templates'],
                'required'    => false,
            ]);
        }

        $builder->add($propertiesForm);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();
            $form = $event->getForm();

            $form->get('properties')
                ->add('displayLogic', ExpressionEngineType::class, [
                    'label' => 'label.display_logic',
                    'prototypes' => $this->getPrototypes($block),
                    'attr' => [
                        'help_text' => 'help_text.display_logic'
                    ]
                ])
                ->add('displayDefaultShow', CheckboxType::class, [
                    'label' => 'label.display_default_show',
                    'attr' => [
                        'align_with_widget'     => true,
                        'help_text'             => 'help_text.display_default_show',
                    ],
                ])
            ;
        });
    }

    /**
     * @return \Opifer\ExpressionEngine\Prototype\Prototype[]
     */
    protected function getPrototypes(Block $block)
    {
        $collection = new PrototypeCollection([
            new OrXPrototype(),
            new AndXPrototype(),
            new EventPrototype('Click Event', 'event.type.click'),
            new TextPrototype('DOM Node Id', 'node.id')
        ]);

        $owner = $block->getOwner();
        $blockChoices = [];

        foreach ($owner->getBlocks() as $member) {
            if ($member instanceof ChoiceFieldBlock) {
                $properties = $member->getProperties();
                $choices = [];
                foreach ($properties['options'] as $option) {
                    $choices[] = new Choice($option['key'], $option['value']);
                }
                $collection->add(new SelectPrototype($properties['label'], $properties['name'], $choices));
            }

            if (!empty($member->getName())) {
                $blockChoices[] = new Choice($member->getName(), $member->getName());
            }
        }

        $collection->add(new SelectPrototype('Block Name', 'block.name', $blockChoices));

        return $collection->all();
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
