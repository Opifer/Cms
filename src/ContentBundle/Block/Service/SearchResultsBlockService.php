<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\SearchResultsBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;

/**
 * SearchResults Block Service
 */
class SearchResultsBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{

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
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Search Results', 'search_results');

        $tool->setIcon('search')
            ->setDescription('Shows search results');

        return $tool;
    }
}
