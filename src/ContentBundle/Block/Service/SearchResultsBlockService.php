<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockManager;
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
     * @param BlockManager $blockManager
     * @param array $config
     */
    public function __construct(EngineInterface $templating, BlockManager $blockManager, array $config)
    {
        $this->templating = $templating;
        $this->blockManager = $blockManager;
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
            'search_results' => $this->getSearchResults()
        ];
           
        return $parameters;
    }

    public function getSearchResults(){
        $search = $this->request->get('search');

        return (!empty($search)) ? $this->blockManager->getRepository()->getContentByValue($search) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Search Results', 'search_results');

        $tool->setIcon('search')
            ->setDescription('Shows search results');

        return $tool;
    }
}
