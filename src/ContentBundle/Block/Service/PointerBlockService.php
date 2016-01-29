<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockManager;
use Opifer\ContentBundle\Block\Tool\ContentTool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Entity\PointerBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Doctrine\ORM\EntityRepository;

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

        $parameters = array(
            'block_service'  => $this,
            'pointer'        => $block,
            'block'          => $block->getReference() ? $block->getReference() : $block,
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

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
//        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('default', 'form', ['inherit_data' => true])
                ->add('reference', 'entity', [
                    'required'      => false,
                    'label'         => 'label.block',
                    'class'         => 'OpiferContentBundle:Block',
                    'property'      => 'sharedDisplayName', // Assuming that the entity has a "name" property
                    'query_builder' => function (EntityRepository $blockRepository) {
                        return $blockRepository->createQueryBuilder('b')
                            ->add('orderBy', 'b.sharedDisplayName ASC')
                            ->andWhere("b.shared = 1")
                            ->andWhere("b.owner IS NULL");
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
        $tool = new ContentTool('Shared block', 'OpiferContentBundle:PointerBlock');

        $tool->setIcon('all_inclusive')
            ->setDescription('This block will load a shared block');

        return $tool;
    }
}
