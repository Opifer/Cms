<?php

namespace Opifer\ContentBundle\Block;

use Opifer\ContentBundle\Block\Tool\ColumnTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Opifer\ContentBundle\Entity\ColumnBlock;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class ColumnBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ColumnBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var integer */
    protected $columnCount = 1;

    /** @var string */
    protected $view = 'OpiferContentBundle:Block:Layout/layout.html.twig';

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {

        $parameters = array(
            'block_service'  => $this,
            'block'          => $block,
            'span_styles'    => $this->getSpanStyles($block),
            'gutter_styles'    => $this->getGutterStyles($block),
        );

        return $this->renderResponse($this->getView($block), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        return $this->renderResponse($this->getManageView($block), array(
            'block_service'  => $this,
            'block'          => $block,
            'block_view'     => $this->getView($block),
            'span_styles'    => $this->getSpanStyles($block),
            'gutter_styles'  => $this->getGutterStyles($block),
            'manage_type'    => $this->getManageFormTypeName(),
        ), $response);
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

            $block->setProperties($properties);

            $form->get('properties')->add('spans', 'span_collection', ['column_count' => $block->getColumnCount(), 'label' => 'label.spans', 'attr' => ['help_text' => 'help.column_spans']]);
            $form->get('properties')->add('gutters', 'gutter_collection', ['column_count' => $block->getColumnCount(), 'label' => 'label.gutters', 'attr' => ['help_text' => 'help.column_gutters']]);

        });
    }

    /**
     * {@inheritDoc}
     */
    public function configureManageOptions(OptionsResolver $resolver)
    {
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
        return new ColumnBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ColumnTool($this->getName(), 'OpiferContentBundle:ColumnBlock');

        $tool->setData(['columnCount' => $this->columnCount])
            ->setIcon('view_column')
            ->setDescription('Inserts ' . $this->columnCount . ' columns equal in width');

        return $tool;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return sprintf('%d columns', $this->getColumnCount());
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columnCount;
    }

    /**
     * @param int $columnCount
     */
    public function setColumnCount($columnCount)
    {
        $this->columnCount = $columnCount;
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
            $properties = $block->getProperties();
            if (isset($properties['spans']) && count($properties['spans']) > 0) {
                foreach ($properties['spans'] as $screen => $cols) {
                    foreach ($cols as $col => $span) {
                        $spanStyles[$col][] = "col-$screen-$span";
                    }
                }
            } else {
                $columnCount = $block->getColumnCount();
                $spanStyles = array_fill_keys(range(0, $columnCount), 'col-xs-'. (12/$columnCount));
            }
        }

        return $spanStyles;
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getGutterStyles(BlockInterface $block)
    {
        $gutterStyles = array();

        if ($block->getColumnCount()) {
            $properties = $block->getProperties();
            if (isset($properties['gutters']) && count($properties['gutters']) > 0) {
                foreach ($properties['gutters'] as $screen => $cols) {
                    foreach ($cols as $col => $span) {
                        $gutterStyles[$col][] = "p-$screen-$span";
                    }
                }
            }
        }

        return $gutterStyles;
    }

}