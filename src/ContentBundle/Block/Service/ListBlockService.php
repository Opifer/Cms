<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\ListBlock;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Content Collection Block Service
 */
class ListBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ContentManagerInterface */
    protected $contentManager;
    protected $view = 'OpiferContentBundle:Block:Content/list.html.twig';

    /**
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct(EngineInterface $templating, ContentManagerInterface $contentManager)
    {
        parent::__construct($templating);

        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        return 'List';
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', 'form', ['virtual' => true])
                ->add('value',  ContentListPickerType::class, [
                    'label'    => 'label.content',
                    'multiple' => true,
                    'data'     => $options['data']->getValue()
                ])
        );
    }

    public function load(BlockInterface $block)
    {
        $collection = $this->contentManager->getRepository()
            ->createQueryBuilder('c')
            ->where('c.id IN (:ids)')->setParameter('ids', json_decode($block->getValue()))
            ->getQuery()
            ->getResult();

        if ($collection) {
            $block->setCollection($collection);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new ListBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new ContentTool('List', 'OpiferContentBundle:ListBlock');

        $tool->setIcon('view_list')
            ->setDescription('Adds references to a collection of content items');

        return $tool;
    }
}
