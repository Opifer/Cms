<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\AbstractBlockService;
use Opifer\ContentBundle\Block\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\CollectionBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Content Collection Block Service
 */
class CollectionBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var EntityManager */
    protected $em;
    protected $view = 'OpiferContentBundle:Block:Content/collection.html.twig';

    /**
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct(EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($templating);

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'Collection';
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
                ->add('order_by', ChoiceType::class, [
                    'label' => 'Order by',
                    'choices' => [
                        'Creation Date' => 'createdAt',
                        'Title' => 'title'
                    ],
                    'choices_as_values' => true,
                ])
                ->add('order_direction', ChoiceType::class, [
                    'label' => 'Order direction',
                    'choices' => [
                        'Ascending' => 'ASC',
                        'Descending' => 'DESC'
                    ],
                    'choices_as_values' => true,
                ])
                ->add('limit', IntegerType::class)
        );
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        $properties = $block->getProperties();

        $qb = $this->em->getRepository('OpiferCmsBundle:Content')
            ->createQueryBuilder('c');

        if (isset($properties['order_by'])) {
            $direction = (isset($properties['order_direction'])) ? $properties['order_direction'] : 'ASC';

            $qb->orderBy('c.'.$properties['order_by'], $direction);
        }

        $limit = (isset($properties['limit'])) ? $properties['limit'] : 10;
        $qb->setMaxResults($limit);

        $collection = $qb->getQuery()->getResult();

        if ($collection) {
            $block->setCollection($collection);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CollectionBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('Collection', 'OpiferContentBundle:CollectionBlock');

        $tool->setIcon('view_list')
            ->setDescription('Adds references to a collection of content items');

        return $tool;
    }
}
