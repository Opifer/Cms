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

        $homePage = $this->contentManager->findOneBySlug('index');
        $currentPage = $this->contentManager->findOneBySlug($block->getOwner()->getSlug());

        //get current page slug to mark it as active when listing
        $parameters['currentPageSlug'] = $currentPage->getSlug();

        $parameters['breadcrumbs'] = $currentPage->getBreadCrumbs();

        // add homepage link as first breadcrumb if not exists in breadcrumbs
        if (!array_key_exists('index', $parameters['breadcrumbs'])) {
            $parameters['breadcrumbs'] = array_merge(['index' => $homePage->getShortTitle()], $parameters['breadcrumbs']);
        }

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
        $tool = new Tool('Breadcrumb', 'breadcrumbs');

        $tool->setIcon('linear_scale')
            ->setDescription('Navigation path of current content');

        return $tool;
    }
}
