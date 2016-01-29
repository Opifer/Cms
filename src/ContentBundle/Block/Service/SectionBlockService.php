<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\ContainerTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\SectionBlock;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SectionBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class SectionBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', 'form', ['inherit_data' => true])
                ->add('header', 'ckeditor', ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('footer', 'ckeditor', ['label' => 'label.footer', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        );

        $propertiesForm = $builder->create('properties', 'form')
            ->add('id', 'text', ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', 'text', ['attr' => ['help_text' => 'help.extra_classes']]);


        $propertiesForm->add('styles', 'choice', [
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
        return 'layout';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new SectionBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContainerTool($this->getName(), 'OpiferContentBundle:SectionBlock');

        $tool
            ->setIcon('crop_landscape')
            ->setDescription('Section element to hold columns or content in');

        return $tool;
    }
}
