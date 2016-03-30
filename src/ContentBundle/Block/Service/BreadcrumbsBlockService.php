<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\BreadcrumbsBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Breadcrumbs Block Service
 */
class BreadcrumbsBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{

    /**
     * @param EngineInterface $templating
     * @param ContentManagerInterface $contentManager
     * @param array $config
     */
    public function __construct(EngineInterface $templating, ContentManagerInterface $contentManager, array $config)
    {
        $this->contentManager = $contentManager;
        $this->templating = $templating;
        $this->config = $config;
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
        ];
        
        $currentPage = $this->contentManager->findOneBySlug($block->getOwner()->getSlug());

        $parameters['breadcrumbs'] = array_merge($currentPage->getBreadCrumbs());
               
        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new BreadcrumbsBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Breadcrumbs', 'breadcrumbs');

        $tool->setIcon('list')
            ->setDescription('Adds breadcrumbs');

        return $tool;
    }
}
