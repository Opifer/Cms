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

/**
 * Class PointerBlockService
 *
 * @package Opifer\ContentBundle\Block
 */
class PointerBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /**
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @param EngineInterface $templating
     * @param BlockManager    $em
     */
    public function __construct(EngineInterface $templating, BlockManager $blockManager)
    {
        parent::__construct($templating);

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
            'block'          => $block->getReference(),
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
        if (!$block) {
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