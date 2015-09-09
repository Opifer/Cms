<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\JumbotronBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class JumbotronBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class JumbotronBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $view = 'OpiferContentBundle:Block:Content/jumbotron.html.twig';

    /** @var array */
    protected $styles;

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Jumbotron';
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', 'form')
            ->add('id', 'text', ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', 'text', ['attr' => ['help_text' => 'help.extra_classes']]);


        if ($this->styles) {

            $propertiesForm
                ->add('styles', 'choice', [
                    'label' => 'label.styling',
                    'choices'  => array_combine($this->styles, $this->styles),
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr' => ['help_text' => 'help.html_styles'],
                ]);
        }

        $builder->add(
            $builder->create('default', 'form', ['inherit_data' => true])
                ->add('media', 'mediapicker', [
                    'required'  => false,
                    'multiple' => false,
                    'property' => 'name',
                    'class' => 'OpiferCmsBundle:Media',
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
        return new JumbotronBlock;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param array $styles
     */
    public function setStyles($styles)
    {
        $this->styles = $styles;
    }



}