<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavigationBlock;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\ContentBundle\Form\Type\ContentTreePickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
     * @param EngineInterface         $templating
     * @param ContentManagerInterface $contentManager
     * @param array                   $config
     */
    public function __construct(EngineInterface $templating, ContentManagerInterface $contentManager, array $config)
    {
        parent::__construct($templating, $config);

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
                    $builder->create('properties', FormType::class)
                        ->add('content', ContentListPickerType::class, [
                            'label' => 'label.custom',
                            'attr' => [
                                'form-group' => [
                                    'styles' => ($showContentPicker === false) ? 'display:none;' : ''
                                ]
                            ],
                            'multiple' => true
                        ])
                        //->add('tree', ContentTreePickerType::class, [
                        //    'label' => 'label.custom',
                        //    'attr' => [
                        //        'form-group' => [
                        //            'styles' => ($showContentPicker === false) ? 'display:none;' : ''
                        //        ]
                        //    ]
                        //])
                        ->add('levels', IntegerType::class, [
                            'label' => 'label.levels',
                            'empty_data' => 1,
                            'attr' => ['help_text' => 'help.levels'],
                        ])
                        ->add('template', ChoiceType::class, [
                            'label' => 'label.template',
                            'placeholder' => 'placeholder.choice_optional',
                            'attr' => ['help_text' => 'help.block_template'],
                            'choices' => $this->config['templates'],
                            'required' => false,
                        ])
                )
        );
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
     * @param  string $json
     * @return array
     */
    public function getCustomTree($json)
    {
        $simpleTree = json_decode($json, true);
        if (!$simpleTree) {
            return [];
        }

        $collection = $this->contentManager->getRepository()
            ->createQueryBuilder('c')
            ->where('c.id IN (:ids)')->setParameter('ids', $this->gatherIds($simpleTree))
            ->getQuery()
            ->getArrayResult(); // TODO Serialize instead of transforming the complete object to array

        $this->setCollection($collection);

        return $this->buildTree($simpleTree);
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
    public function getTool()
    {
        $tool = new ContentTool('Navigation', 'OpiferContentBundle:NavigationBlock');

        $tool->setIcon('menu')
            ->setDescription('Generates a simple page navigation');

        return $tool;
    }
}
