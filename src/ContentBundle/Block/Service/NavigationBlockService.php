<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavigationBlock;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Navigation Block Service
 */
class NavigationBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    protected $collection;

    /**
     * Constructor
     *
     * @param BlockRenderer         $blockRenderer
     * @param ContentManagerInterface $contentManager
     * @param array                   $config
     */
    public function __construct(BlockRenderer $blockRenderer, ContentManagerInterface $contentManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $showContentPicker = false;
        if ($options['data'] && $options['data']->getValue() == NavigationBlock::CHOICE_CUSTOM) {
            $showContentPicker = true;
        }

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('value', ChoiceType::class, [
                    'label' => 'label.navigation_block_value',
                    'choices' => [
                        'Top level pages' => NavigationBlock::CHOICE_TOP_LEVEL,
                        'Custom selection' => NavigationBlock::CHOICE_CUSTOM
                    ],
                    'choices_as_values' => true,
                    'attr' => ['class' => 'toggle-content-picker']
                ])
                ->add(
                    $builder->create('properties', FormType::class, ['label' => false, 'attr' => ['widget_col' => 12]])
                        ->add('content', ContentListPickerType::class, [
                            'label' => 'label.custom',
                            'attr' => [
                                'widget_col' => 9,
                                'form_group' => [
                                    'styles' => ($showContentPicker === false) ? 'display:none;' : ''
                                ]
                            ]
                        ])
                        //->add('tree', ContentTreePickerType::class, [
                        //    'label' => 'label.custom',
                        //    'attr' => [
                        //        'form-group' => [
                        //            'styles' => ($showContentPicker === false) ? 'display:none;' : ''
                        //        ]
                        //    ]
                        //])
                        ->add('levels', ChoiceType::class, [
                            'label' => 'label.levels',
                            'choices' => [0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5],
                            'attr' => [
                                'help_text' => 'help.levels',
                                'widget_col' => 9,
                            ],
                        ])
                )
        );

        $builder->get('styles')
            ->add('template', ChoiceType::class, [
                'label' => 'label.template',
                'attr' => ['help_text' => 'help.block_template', 'widget_col' => 9],
                'choices' => $this->config['templates'],
                'required' => true,
            ])
        ;
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        $levels = (isset($block->getProperties()['levels'])) ? $block->getProperties()['levels'] : 1;

        /** @var NavigationBlock $block */
        if ($block->getValue() == NavigationBlock::CHOICE_CUSTOM && isset($block->getProperties()['content'])) {
            $ids = json_decode($block->getProperties()['content'], true);

            $collection = $this->contentManager->getRepository()->findByLevels($levels, $ids);

            $block->setTree($collection);
        } elseif ($block->getValue() == NavigationBlock::CHOICE_TOP_LEVEL) {
            $collection = $this->contentManager->getRepository()->findByLevels($levels);

            $block->setTree($collection);
        }
    }

    /**
    * @param array $collection
    * @param array $sort
    *
    * @return array
    */
    public function getOrdered($collection, $sort = null)
    {
        if (!$sort) {
            return $collection;
        }

        $unordered = [];
        foreach ($collection as $content) {
            $unordered[$content->getId()] = $content;
        }

        $ordered = [];
        foreach ($sort as $id) {
            if (isset($unordered[$id])) {
                $ordered[] = $unordered[$id];
            }
        }

        return $ordered;
    }

    /**
     * Gather all ids, so we can retrieve all content in a single query
     *
     * @param array $array
     * @param array $ids
     * @return array
     */
    protected function gatherIdsFromTree(array $array, array $ids = array())
    {
        foreach ($array as $item) {
            $ids[] = $item['id'];
            if (isset($item['__children']) && count($item['__children'])) {
                $ids = $this->gatherIdsFromTree($item['__children'], $ids);
            }
        }

        return $ids;
    }

    /**
     * Keep collection as key-value
     *
     * @param $collection
     */
    protected function setCollection($collection)
    {
        $array = [];
        foreach ($collection as $content) {
            $array[$content['id']] = $content;
        }

        $this->collection = $array;
    }

    /**
     * Build the tree from the simpletree and the collection
     *
     * @param array $simpleTree
     * @param array $tree
     * @return array
     */
    protected function buildCustomTree(array $simpleTree, $tree = [])
    {
        foreach ($simpleTree as $item) {
            if (!isset($this->collection[$item['id']])) {
                continue;
            }

            $content = $this->collection[$item['id']];

            unset($this->collection[$item['id']]); // TODO Fix multi-usage of single item

            if (isset($item['__children']) && count($item['__children'])) {
                $content['__children'] = $this->buildCustomTree($item['__children']);
            } else {
                $content['__children'] = [];
            }

            $tree[] = $content;
        }

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new NavigationBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Navigation', 'navigation');

        $tool->setIcon('menu')
            ->setDescription('Create different kinds of navigation lists');

        return $tool;
    }
}
