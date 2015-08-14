<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Entity\LayoutBlock;
use Opifer\ContentBundle\Form\Type\ColumnSpanType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class LayoutBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class LayoutBlockService extends AbstractBlockService implements BlockServiceInterface
{
    /** @var string */
    protected $view = 'OpiferContentBundle:Block:Layout/layout.html.twig';

    /** @var string */
    protected $editView = 'OpiferContentBundle:PageManager:edit_layout_block.html.twig';

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {

        $parameters = array(
            'block_service'  => $this,
            'block'          => $block,
            'span_styles'    => $this->getSpanStyles($block),
        );

        return $this->renderResponse($this->getView(), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getManageView(), array(
            'block_service'  => $this,
            'block'          => $block,
            'block_view'     => $this->getView(),
            'span_styles'    => $this->getSpanStyles($block),
            'manage_type'    => $this->getManageFormTypeName(),
        ), $response);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return $block->getWrapper() ?: sprintf('%d columns', $block->getColumnCount());
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

        $builder->add($propertiesForm);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();

            $form = $event->getForm();

            if ($block->getWrapper() == 'section') {
                $styles = ['row-space-top-2', 'row-space-top-4', 'row-space-top-8', 'row-space-2', 'row-space-4', 'row-space-8', 'light', 'dark'];
                $form->get('properties')->add('styles', 'choice', [
                        'label' => 'label.styling',
                        'choices'  => array_combine($styles, $styles),
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'attr' => ['help_text' => 'help.html_styles'],
                    ]);
            }
            if (!$block->getColumnCount()) {
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
            }

            if ($block->getColumnCount()) {
                $styles = ['row-space-top-2', 'row-space-top-4', 'row-space-top-8', 'row-space-2', 'row-space-4', 'row-space-8'];
                $form->get('properties')->add('styles', 'choice', [
                    'label' => 'label.styling',
                    'choices'  => array_combine($styles, $styles),
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr'     => ['help_text' => 'help.html_styles'],
                ]);

                // @todo: Replace with a nice getDefaultOptions method
                $properties = $block->getProperties();
                if (!isset($properties['styles']) ) {
                    $properties['styles'] = array();
                }

                if (!isset($properties['spans']) || !count($properties['spans'])) {
                    $sizeKeys = range(0, $block->getColumnCount()-1);
                    $properties['spans'] = array_fill_keys($sizeKeys, array_fill_keys(['xs','sm', 'md', 'lg'], 12/$block->getColumnCount()));
                }

                $block->setProperties($properties);

                $form->get('properties')->add('spans', 'collection', ['type' => new ColumnSpanType(), 'options' => ['label' => false], 'attr' => ['help_text' => 'help.column_spans']]);
            }
        });
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getSpanStyles(BlockInterface $block)
    {
        $spanStyles = array();

        if ($block->getColumnCount()) {
            $columnCount = $block->getColumnCount();
            $properties = $block->getProperties();
            if (isset($properties['spans']) && count($properties['spans']) > 0) {
                for ($i = 0; $i < $columnCount; $i++) {
                    array_walk($properties['spans'][$i], function(&$item, $key) {
                        $item =  'col-'.$key.'-'.$item;
                    });

                    $spanStyles[$i] = implode(' ', $properties['spans'][$i]);
                }
            } else {
                $spanStyles = array_fill_keys(range(0, $columnCount), 'col-xs-'. (12/$columnCount));
            }
        }

        return $spanStyles;
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
        return new LayoutBlock;
    }
}