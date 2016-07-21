<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Model\Content;
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
    /** @var RequestStack */
    protected $requestStack;

    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * @param BlockRenderer $blockRenderer
     * @param ContentManagerInterface $contentManager
     * @param array $config
     */
    public function __construct(BlockRenderer $blockRenderer, ContentManagerInterface $contentManager, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new SearchResultsBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
            'searchResults' => $this->getSearchResults()
        ];
           
        return $parameters;
    }

    /**
     * Get the search results
     *
     * @return Content[]
     */
    public function getSearchResults()
    {
        $term = $this->getRequest()->get('search', '');

        return $this->contentManager->getRepository()->search($term);
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

    /**
     * @param RequestStack $requestStack
     */
    public function setRequest(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return null|Request
     */
    public function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
