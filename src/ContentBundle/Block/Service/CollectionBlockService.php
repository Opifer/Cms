<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CollectionBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Model\ContentTypeInterface;
use Opifer\ContentBundle\Model\ContentTypeManager;
use Opifer\ExpressionEngine\DoctrineExpressionEngine;
use Opifer\ExpressionEngine\Form\Type\ExpressionEngineType;
use Opifer\ExpressionEngine\Prototype\AndXPrototype;
use Opifer\ExpressionEngine\Prototype\Choice;
use Opifer\ExpressionEngine\Prototype\OrXPrototype;
use Opifer\ExpressionEngine\Prototype\Prototype;
use Opifer\ExpressionEngine\Prototype\PrototypeCollection;
use Opifer\ExpressionEngine\Prototype\SelectPrototype;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Webmozart\Expression\Constraint\Equals;
use Webmozart\Expression\Constraint\NotEquals;

/**
 * Content Collection Block Service.
 */
class CollectionBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /** @var ContentTypeManager */
    protected $contentTypeManager;

    /** @var DoctrineExpressionEngine */
    protected $expressionEngine;

    /**
     * @param BlockRenderer         $blockRenderer
     * @param ContentManagerInterface $contentManager
     * @param array                   $config
     */
    public function __construct(BlockRenderer $blockRenderer, DoctrineExpressionEngine $expressionEngine, ContentManagerInterface $contentManager, ContentTypeManager $contentTypeManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->expressionEngine = $expressionEngine;
        $this->contentManager = $contentManager;
        $this->contentTypeManager = $contentTypeManager;
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
                    'prototypes' => $this->getPrototypes()
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
                ->add('template', ChoiceType::class, [
                    'label' => 'label.template',
                    'placeholder' => 'placeholder.choice_optional',
                    'attr' => ['help_text' => 'help.block_template'],
                    'choices' => $this->config['templates'],
                    'required' => false,
                ])
        );
    }

    /**
     * @return \Opifer\ExpressionEngine\Prototype\Prototype[]
     */
    protected function getPrototypes()
    {
        $collection = new PrototypeCollection([
            new OrXPrototype(),
            new AndXPrototype(),
            new SelectPrototype('Content Type', 'contentType.id', $this->getContentTypeChoices()),
            new SelectPrototype('Status', 'active', [
                new Choice(true, 'Active'),
                new Choice(false, 'Inactive'),
            ])
        ]);

        return $collection->all();
    }

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
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        $this->loadCollection($block);
    }

    /**
     * Load the collection if any conditions are defined
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
