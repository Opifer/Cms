<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\ContainerTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ContainerBlock;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class ColumnBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ContainerBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);


        $propertiesForm = $builder->create('properties', 'form')
            ->add('id', 'text', ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', 'text', ['attr' => ['help_text' => 'help.extra_classes']]);

        $builder->add($propertiesForm);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();

            $form = $event->getForm();

            $form->get('properties')->add('styles', 'choice', [
                'label' => 'label.styling',
                'choices'  => $this->config['styles'],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help_text' => 'help.html_styles'],
            ]);

            $form->get('properties')->add(
                'container_size',
                'choice',
                [
                    'label' => 'label.container_sizing',
                    'choices' => ['fluid' => 'label.container_fluid', '' => 'label.container_fixed', 'smooth' => 'label.container_smooth'],
                    'required' => true,
                    'attr' => ['help_text' => 'help.container_sizing'],
                ]
            );
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
    public function getTool()
    {
        $tool = new ContainerTool($this->getName(), 'OpiferContentBundle:ContainerBlock');

        $tool
            ->setIcon('crop_landscape')
            ->setDescription('Container element to hold columns or other blocks in');

        return $tool;
    }
}
