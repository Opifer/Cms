<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\SectionBlock;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Section Block Service
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
            $builder->create('default', FormType::Class, ['inherit_data' => true])
                ->add('header', CKEditorType::class, ['label' => 'label.header', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
                ->add('footer', CKEditorType::class, ['label' => 'label.footer', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
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

        $propertiesForm->add('container_size', ChoiceType::class, [
            'label' => 'label.container_sizing',
            'choices' => ['fluid' => 'label.container_fluid', '' => 'label.container_fixed', 'smooth' => 'label.container_smooth'],
            'required' => true,
            'attr' => ['help_text' => 'help.container_sizing'],
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
        $tool = new Tool($this->getName(), 'OpiferContentBundle:SectionBlock');

        $tool
            ->setIcon('crop_din')
            ->setGroup(Tool::GROUP_LAYOUT)
            ->setDescription('Section element to hold columns or content in');

        return $tool;
    }
}
