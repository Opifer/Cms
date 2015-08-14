<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\ButtonBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ButtonBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ButtonBlockService extends AbstractBlockService implements BlockServiceInterface
{
    protected $view = 'OpiferContentBundle:Block:Content/button.html.twig';

    /** @var array */
    protected $styles;

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Button';
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', 'form')
            ->add('url', 'text', ['label' => 'label.url'])
            ->add(
                'target',
                'choice',
                [
                    'label' => 'label.target',
                    'choices' => ['_blank' => '_blank', '_self' => '_self'],
                    'required' => false,
                ]
            )
            ->add('id', 'text', ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', 'text', ['attr' => ['help_text' => 'help.extra_classes']]);


        if ($this->styles) {
            $propertiesForm->add('styles', 'choice', [
                'label' => 'label.styling',
                'choices'  => array_combine($this->styles, $this->styles),
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ]);
        }

        $builder->add(
            $builder->create('default', 'form', ['inherit_data' => true])
                ->add('value', 'text', ['label' => 'label.label'])
        )->add(
            $propertiesForm
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ButtonBlock;
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