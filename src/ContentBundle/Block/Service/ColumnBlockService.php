<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ColumnBlock;
use Opifer\ContentBundle\Form\Type\GutterCollectionType;
use Opifer\ContentBundle\Form\Type\SpanCollectionType;
use Opifer\ContentBundle\Form\Type\StylesType;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColumnBlockService.
 */
class ColumnBlockService extends AbstractBlockService implements LayoutBlockServiceInterface, BlockServiceInterface, ToolsetMemberInterface
{
    /** @var int */
    protected $columnCount = 1;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * ColumnBlockService constructor.
     *
     * @param BlockRenderer $blockRenderer
     * @param array         $config
     * @param EntityManager $entityManager
     */
    public function __construct(BlockRenderer $blockRenderer, array $config, EntityManager $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct($blockRenderer, $config);
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = parent::getViewParameters($block);

        $classes = array(
            'column_classes' => $this->getColumnClasses($block),
            'offset_classes' => $this->getOffsetClasses($block),
            'gutter_classes' => $this->getGutterClasses($block),
        );

        return array_merge($parameters, $classes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id'],'required' => false])
            ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes'],'required' => false])
        ;

        $builder->get('default')
            ->add('column_count', ChoiceType::class, [
                'label' => 'label.column_count',
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'help_text' => 'help.column_count',
                    'buttongroup' => true,
                    'tag' => 'styles'
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $block = $event->getData();

            $form = $event->getForm();

            // @todo: Replace with a nice getDefaultOptions method
            $properties = $block->getProperties();
            if (!count($properties)) {
                $properties['styles'] = [];
                $cols = array_merge([array_fill(0, $block->getColumnCount(), 12 / $block->getColumnCount())], array_fill(0, 4, array_fill(0, $block->getColumnCount(), null)));
                $keys = ['xs', 'sm', 'md', 'lg', 'xl'];
                $properties['spans'] = array_combine($keys, $cols);
                $properties['offsets'] = array_combine($keys, array_fill(0, 5, array_fill(0, $block->getColumnCount(), null)));
                $properties['gutters'] = array_combine($keys, $cols);
                $block->setProperties($properties);
            }

            // Quickfix to set xl spans and offsets on already existing blocks
            if (!isset($properties['spans']['xl'])) {
                $properties['spans']['xl'] = [null, null];
                $block->setProperties($properties);
            }
            if (!isset($properties['offsets']['xl'])) {
                $properties['offsets']['xl'] = [null, null];
                $block->setProperties($properties);
            }

            $form->get('properties')
                ->add('styles', StylesType::class, [
                    'choices' => $this->config['styles'],
                    'attr' => [
                        'help_text' => 'help.list_display_size',
                        'tag' => 'styles'
                    ],
                ])
                ->add('spans', SpanCollectionType::class, [
                    'column_count' => $block->getColumnCount(),
                    'label' => 'label.spans',
                    'attr' => [
                        'help_text' => 'help.column_spans',
                        'tag' => 'styles'
                    ],
                    'required' => false,
                ])
                ->add('offsets', SpanCollectionType::class, [
                    'column_count' => $block->getColumnCount(),
                    'label' => 'label.offsets',
                    'attr' => [
                        'help_text' => 'help.column_offsets',
                        'tag' => 'styles'
                    ],
                    'required' => false,
                ])
                ->add('gutters', GutterCollectionType::class, [
                    'column_count' => $block->getColumnCount(),
                    'label' => 'label.gutters',
                    'attr' => [
                        'help_text' => 'help.column_gutters',
                        'tag' => 'styles'
                    ],
                    'required' => false,
                ])
            ;
        });
    }

    public function preUpdate(BlockInterface $block)
    {
        $children = $block->getChildren();

        if (!$children) {
            return;
        }

        foreach ($children as $child) {
            $child->getPosition();

            if ($child->getPosition() > ($block->getColumnCount() -1)) {
                $child->setPosition(($block->getColumnCount() -1));
                $this->em->persist($child);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getManageFormTypeName()
    {
        return 'layout';
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock($args)
    {
        $block = new ColumnBlock();

        unset($args['owner']);

        foreach ($args as $attribute => $value) {
            $reflProp = new \ReflectionProperty($block, $attribute);
            $reflProp->setAccessible(true);
            $reflProp->setValue($block, $value);
        }

        return $block;
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool($block !== null ? sprintf('%d columns', $block->getColumnCount()) : 'label.columnblock', 'column');

        $tool->setIcon('view_column')
            ->setGroup(Tool::GROUP_LAYOUT)
            ->setDescription('caption.columnblock');

        return $tool;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return sprintf('%d columns', $block !== null ? $block->getColumnCount() : $this->getColumnCount());
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
    public function getColumnClasses(BlockInterface $block)
    {
        $spanStyles = array();

        if ($block->getColumnCount()) {
            $properties = $block->getProperties();
            if (isset($properties['spans']) && count($properties['spans']) > 0) {
                foreach ($properties['spans'] as $screen => $cols) {
                    if (!$cols) {
                        continue;
                    }
                    foreach ($cols as $col => $span) {
                        if (empty($span)) {
                            continue;
                        }
                        $spanStyles[$col][] = "col-$screen-$span";
                    }
                }
            } else {
                $columnCount = $block->getColumnCount();
                $spanStyles = array_fill_keys(range(0, $columnCount), 'col-xs-'.(12 / $columnCount));
            }
        }

        return $spanStyles;
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getOffsetClasses(BlockInterface $block)
    {
        $classes = array();

        if ($block->getColumnCount()) {
            $properties = $block->getProperties();
            if (isset($properties['offsets']) && count($properties['offsets']) > 0) {
                foreach ($properties['offsets'] as $screen => $cols) {
                    if (!$cols) {
                        continue;
                    }
                    foreach ($cols as $col => $span) {
                        if (empty($span)) {
                            continue;
                        }
                        $classes[$col][] = "col-$screen-offset-$span";
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getGutterClasses(BlockInterface $block)
    {
        $gutterStyles = array();

        if ($block->getColumnCount()) {
            $properties = $block->getProperties();
            if (isset($properties['gutters']) && count($properties['gutters']) > 0) {
                foreach ($properties['gutters'] as $screen => $cols) {
                    if (!$cols) {
                        continue;
                    }
                    foreach ($cols as $col => $span) {
                        if ($span === null) {
                            continue;
                        }
                        $gutterStyles[$col][] = "px-$screen-$span";
                    }
                }
            }
        }

        return $gutterStyles;
    }

    public function getPlaceholders(BlockInterface $block = null)
    {
        $placeholders = [];

        for ($i = 0;$i < $block->getColumnCount();++$i) {
            $placeholders[$i] = sprintf('Column %d', $i + 1);
        }

        return $placeholders;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'description.columnblock';
    }
}
