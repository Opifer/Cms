<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ContainerBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ColumnBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ContainerBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);


        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        $builder->add($propertiesForm);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();

            $form = $event->getForm();

            $form->get('properties')->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ]);

            $form->get('properties')->add('container_size', ChoiceType::class, [
                'label' => 'label.container_sizing',
                'choices' => ['fluid' => 'label.container_fluid', '' => 'label.container_fixed', 'smooth' => 'label.container_smooth'],
                'required' => true,
                'attr' => ['help_text' => 'help.container_sizing'],
            ]);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ContainerBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($this->getName(), 'container');

        $tool
            ->setIcon('crop_free')
            ->setGroup(Tool::GROUP_LAYOUT)
            ->setDescription('Container element to hold columns or other blocks in');

        return $tool;
    }

    /**
     * {@inheritDoc}
     */
    public function getPlaceholders(BlockInterface $block = null)
    {
        return [0 => 'container'];
    }
}
