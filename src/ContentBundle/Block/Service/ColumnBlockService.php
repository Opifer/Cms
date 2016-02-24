<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ColumnBlock;
use Opifer\ContentBundle\Form\Type\GutterCollectionType;
use Opifer\ContentBundle\Form\Type\SpanCollectionType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColumnBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class ColumnBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{
    /** @var integer */
    protected $columnCount = 1;

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

        $propertiesForm = $builder->create('properties', FormType::class)
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']]);

        $builder->add($propertiesForm);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();

            $form = $event->getForm();

            // @todo: Replace with a nice getDefaultOptions method
            $properties = $block->getProperties();
            if (!count($properties) ) {
                $properties['styles'] = array();
                $cols = array_merge(array(array_fill(0, $block->getColumnCount(), 12/$block->getColumnCount())), array_fill(0, 3, array_fill(0, $block->getColumnCount(), null)));
                $keys = array('xs', 'sm', 'md', 'lg');
                $properties['spans'] = array_combine($keys, $cols);
                $properties['gutters'] = array_combine($keys, $cols);
                $block->setProperties($properties);
            }

            $styles = ['row-space-top-2', 'row-space-top-4', 'row-space-top-8', 'row-space-2', 'row-space-4', 'row-space-8'];
            $form->get('properties')->add('styles', ChoiceType::class, [
                'label' => 'label.styling',
                'choices'  => $styles,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'attr'     => ['help_text' => 'help.html_styles'],
            ]);


            $form->get('properties')->add('spans', SpanCollectionType::class, ['column_count' => $block->getColumnCount(), 'label' => 'label.spans', 'attr' => ['help_text' => 'help.column_spans']]);
            $form->get('properties')->add('gutters', GutterCollectionType::class, ['column_count' => $block->getColumnCount(), 'label' => 'label.gutters', 'attr' => ['help_text' => 'help.column_gutters']]);

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
        $tool = new Tool($this->getName(), 'OpiferContentBundle:ColumnBlock');

        $tool->setData(['columnCount' => $this->columnCount])
            ->setGroup(Tool::GROUP_LAYOUT)
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
                        if (empty($span)) {
                            continue;
                        }
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

    public function getPlaceholders(BlockInterface $block = null)
    {
        $placeholders = [];

        for($i=0;$i<$block->getColumnCount();$i++) {
            $placeholders[$i] = sprintf('Column %d', $i+1);
        }

        return $placeholders;
    }
}
