<?php

namespace Opifer\ContentBundle\Block\Service;

use Doctrine\ORM\EntityRepository;
use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PointerBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class PointerBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var BlockManager */
    protected $blockManager;

    /**
     * @param EngineInterface $templating
     * @param BlockManager    $em\
     * @param array           $config
     */
    public function __construct(EngineInterface $templating, BlockManager $blockManager, array $config)
    {
        parent::__construct($templating, $config);

        $this->blockManager = $blockManager;
    }
    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, Response $response = null)
    {
        $this->load($block);

        $parameters = array(
            'block_service'  => $this,
            'block'          => $block->getReference(),
        );

        return $this->renderResponse($this->getView($block), $parameters,  $response);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(BlockInterface $block, Response $response = null)
    {
        $this->load($block);

        if ($block->getReference()) {
            $reference = $block->getReference();
//            $reference->setOwner($block->getOwner()); // needed when managing (manage_tags uses this)
        } else {
            $reference = $block;
        }

        $parameters = array(
            'block_service'  => $this,
            'pointer'        => $block,
            'block'          => $reference,
            'block_view'     => $this->getView($block),
            'manage_type'    => $this->getManageFormTypeName(),
        );

        return $this->renderResponse($this->getManageView($block), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getView(BlockInterface $block)
    {
        if (!$block->getReference()) {
            return $this->config['view'];
        }

        return $this->getReferenceService($block)->getView($block->getReference());
    }

    public function getReferenceService(BlockInterface $block)
    {
        return $this->blockManager->getService($block->getReference()->getBlockType());
    }

    /**
     * {@inheritDoc}
     */
    public function getName(BlockInterface $block = null)
    {
        if (!$block || !$block->getReference()) {
            return 'Shared';
        }

        return sprintf('%s (shared)', $this->getReferenceService($block)->getName($block->getReference()));
    }

    public function allowShare(BlockInterface $block)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
//        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', FormType::class, ['inherit_data' => true])
                ->add('reference', EntityType::class, [
                    'required'      => false,
                    'label'         => 'label.block',
                    'class'         => 'OpiferContentBundle:Block',
                    'property'      => 'sharedDisplayName', // Assuming that the entity has a "name" property
                    'query_builder' => function (EntityRepository $blockRepository) {
                        return $blockRepository->createQueryBuilder('b')
                            ->add('orderBy', 'b.sharedDisplayName ASC')
                            ->andWhere("b.shared = 1")
                            ->andWhere("b.content IS NULL")
                            ->andWhere("b.template IS NULL");
                        ;
                    },
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getManageFormTypeName()
    {
        return 'pointer';
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new PointerBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new Tool('Shared block', 'pointer');

        $tool->setIcon('all_inclusive')
            ->setDescription('This block will load a shared block');

        return $tool;
    }

    public function getPlaceholders(BlockInterface $block = null)
    {
        return [0 => 'Reference'];
    }
}
