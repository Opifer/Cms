<?php

namespace Opifer\CmsBundle\Block\Service;

use Opifer\CmsBundle\Entity\ReviewBlock;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ReviewBundle\Manager\ReviewManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Review Block Service.
 */
class ReviewBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var ReviewManager */
    protected $reviewManager;

    /**
     * Constructor.
     *
     * @param BlockRenderer $blockRenderer
     * @param array           $config
     * @param ReviewManager   $reviewManager
     */
    public function __construct(BlockRenderer $blockRenderer, array $config, ReviewManager $reviewManager)
    {
        parent::__construct($blockRenderer, $config);

        $this->reviewManager = $reviewManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add($builder->create('default', FormType::class, ['inherit_data' => true])

        )->add($builder->create('properties', FormType::class)
            ->add('limit', IntegerType::class, ['label' => 'label.limit', 'attr' => ['help_text' => 'help.limit']])
        );
    }

    public function getViewParameters(BlockInterface $block)
    {
        $properties = $block->getProperties();
        $limit = (isset($properties['limit'])) ? $properties['limit'] : 5;

        $reviews = $this->reviewManager->getRepository()->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $parameters = parent::getViewParameters($block);
        $parameters['reviews'] = $reviews;

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new ReviewBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Reviews', 'review');

        $tool->setIcon('comment')
            ->setDescription('A list of reviews');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     *
     * @return string
     */
    public function getView(BlockInterface $block)
    {
        return 'OpiferReviewBundle:Review:list.html.twig';
    }
}
