<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CarouselSlideBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Carousel Slide Block Service
 */
class CarouselSlideBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
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


        if ($this->config['styles']) {

            $propertiesForm
                ->add('styles', 'choice', [
                    'label' => 'label.styling',
                    'choices'  => array_combine($this->config['styles'], $this->config['styles']),
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => ['help_text' => 'help.html_styles'],
                ]);
        }

        $builder->add(
            $builder->create('default', 'form', ['inherit_data' => true])
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                ])
                ->add('value', 'ckeditor', ['label' => 'label.rich_text', 'attr' => ['label_col' => 12, 'widget_col' => 12]])
        )->add(
            $propertiesForm
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CarouselSlideBlock();
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->config['styles'];
    }

    /**
     * @param array $styles
     */
    public function setStyles($styles)
    {
        $this->config['styles'] = $styles;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('Carousel slide', 'OpiferContentBundle:CarouselSlideBlock');

        $tool->setIcon('filter')
            ->setDescription('A basic carousel slide');

        return $tool;
    }
}
