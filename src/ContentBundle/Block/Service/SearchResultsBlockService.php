<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Model\ContentManagerInterface;
use Opifer\ContentBundle\Entity\SearchResultsBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * SearchResults Block Service
 */
class SearchResultsBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var Request */
    protected $request;

    /**
     * @param EngineInterface $templating
     * @param ContentManagerInterface $contentManager
     * @param array $config
     */
    public function __construct(EngineInterface $templating, ContentManagerInterface $contentManager, array $config)
    {
        $this->templating = $templating;
        $this->contentManager = $contentManager;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new SearchResultsBlock;
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
            'searchResults' => $this->getSearchResults()
        ];
           
        return $parameters;
    }

    public function getSearchResults(){
        $search = $this->request->get('search');

        return (!empty($search)) ? $this->contentManager->getRepository()->getRelatedContentToBlocks($search) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Search results', 'search_results');

        $tool->setIcon('search')
            ->setDescription('Lists search results from a user query');

        return $tool;
    }
}
