<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Opifer\Revisions\Mapping\Annotation as Revisions;
use Opifer\ContentBundle\Entity\Block;

/**
 * SearchResultsBlock
 *
 * @ORM\Entity
 */
class SearchResultsBlock extends Block
{

    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'search_results';
    }
}
