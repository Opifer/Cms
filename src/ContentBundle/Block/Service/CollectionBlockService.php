<?php

namespace Opifer\ContentBundle\Block\Service;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CollectionBlock;
use Opifer\ContentBundle\Form\Type\FilterType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Model\ContentTypeInterface;
use Opifer\ContentBundle\Model\ContentTypeManager;
use Opifer\EavBundle\Model\Attribute;
use Opifer\EavBundle\Model\AttributeManager;
use Opifer\ExpressionEngine\DoctrineExpressionEngine;
use Opifer\ExpressionEngine\Form\Type\ExpressionEngineType;
use Opifer\ExpressionEngine\Prototype\AndXPrototype;
use Opifer\ExpressionEngine\Prototype\Choice;
use Opifer\ExpressionEngine\Prototype\OrXPrototype;
use Opifer\ExpressionEngine\Prototype\PrototypeCollection;
use Opifer\ExpressionEngine\Prototype\SelectPrototype;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content Collection Block Service.
 */
class CollectionBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /** @var ContentTypeManager */
    protected $contentTypeManager;

    /** @var AttributeManager */
    protected $attributeManager;

    /** @var DoctrineExpressionEngine */
    protected $expressionEngine;

    /**
     * Constructor
     *
     * @param BlockRenderer $blockRenderer
     * @param DoctrineExpressionEngine $expressionEngine
     * @param ContentManagerInterface $contentManager
     * @param ContentTypeManager $contentTypeManager
     * @param AttributeManager $attributeManager
     * @param array $config
     */
    public function __construct(
        BlockRenderer $blockRenderer,
        DoctrineExpressionEngine $expressionEngine,
        ContentManagerInterface $contentManager,
        ContentTypeManager $contentTypeManager,
        AttributeManager $attributeManager,
        array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->expressionEngine = $expressionEngine;
        $this->contentManager = $contentManager;
        $this->contentTypeManager = $contentTypeManager;
        $this->attributeManager = $attributeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('conditions', ExpressionEngineType::class, [
                    'prototypes' => $this->getPrototypes(),
                ])
                ->add('order_by', ChoiceType::class, [
                    'label' => 'Order by',
                    'choices' => [
                        'Creation Date' => 'createdAt',
                        'Title' => 'title',
                    ],
                    'choices_as_values' => true,
                ])
                ->add('order_direction', ChoiceType::class, [
                    'label' => 'Order direction',
                    'choices' => [
                        'Ascending' => 'ASC',
                        'Descending' => 'DESC',
                    ],
                    'choices_as_values' => true,
                ])
                ->add('limit', IntegerType::class)
                ->add('filters', BootstrapCollectionType::class, [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'type' => FilterType::class,
                    'attr' => ['help_text' => 'Filters the user can use to search the collection'],
                ])
                ->add('load_more', CheckboxType::class, [
                    'label' => 'Load more',
                    'required' => false,
                    'attr' => [
                        'align_with_widget' => true,
                        'help_text' => 'Adds a `load more` button to the block'
                    ],
                ])
        );

        $builder->get('styles')
            ->add('template', ChoiceType::class, [
                'label' => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr' => ['help_text' => 'help.block_template'],
                'choices' => $this->config['templates'],
                'required' => false,
            ]);
    }

    /**
     * @return \Opifer\ExpressionEngine\Prototype\Prototype[]
     */
    protected function getPrototypes()
    {
        $collection = new PrototypeCollection([
            new OrXPrototype(),
            new AndXPrototype(),
            new SelectPrototype('contenttype_id', 'Content Type', 'contentType.id', $this->getContentTypeChoices()),
            new SelectPrototype('status', 'Status', 'active', [
                new Choice(true, 'Active'),
                new Choice(false, 'Inactive'),
            ]),
        ]);

        $this->addAttributeChoices($collection);

        return $collection->all();
    }

    /**
     * @param PrototypeCollection $collection
     * @throws \Exception
     */
    protected function addAttributeChoices(PrototypeCollection $collection)
    {
        /** @var Attribute $attribute */
        foreach ($this->attributeManager->getRepository()->findAll() as $attribute) {
            if (!$attribute->hasOptions()) {
                continue;
            }

            $options = [];
            foreach ($attribute->getOptions() as $option) {
                $options[] = new Choice($option->getId(), $option->getDisplayName());
            }

            $collection->add(new SelectPrototype(
                'attribute_'.$attribute->getId(),
                $attribute->getDisplayName(),
                'valueSet.values.options.id',
                $options
            ));
        }
    }

    /**
     * @return array
     */
    protected function getContentTypeChoices()
    {
        $choices = [];

        /** @var ContentTypeInterface[] $contentTypes */
        $contentTypes = $this->contentTypeManager->getRepository()->findAll();

        foreach ($contentTypes as $contentType) {
            $choices[] = new Choice($contentType->getId(), $contentType->getName());
        }

        return $choices;
    }

    /**
     * We load the collection on the Execute instead of the Load method to avoid loading the collection
     * on API serialisation.
     *
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null, array $parameters = [])
    {
        $this->loadCollection($block);

        return parent::execute($block, $response, $parameters);
    }

    /**
     * Load the collection if any conditions are defined.
     *
     * @param BlockInterface $block
     */
    protected function loadCollection(BlockInterface $block)
    {
        $properties = $block->getProperties();

        $conditions = (isset($properties['conditions'])) ? $properties['conditions'] : '[]';
        $conditions = $this->expressionEngine->deserialize($conditions);

        if (empty($conditions)) {
            return;
        }

        $qb = $this->expressionEngine->toQueryBuilder($conditions, $this->contentManager->getClass());

        if (isset($properties['order_by'])) {
            $direction = (isset($properties['order_direction'])) ? $properties['order_direction'] : 'ASC';

            $qb->orderBy('a.'.$properties['order_by'], $direction);
        }

        $limit = (isset($properties['limit'])) ? $properties['limit'] : 10;
        $qb->setMaxResults($limit);

        $collection = $qb->getQuery()->getResult();

        if ($collection) {
            $block->setCollection($collection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new CollectionBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Collection', 'collection');

        $tool->setIcon('query_builder')
            ->setDescription('Adds references to a collection of content items');

        return $tool;
    }
}
