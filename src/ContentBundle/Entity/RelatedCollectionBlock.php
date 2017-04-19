<?php

namespace Opifer\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Related Collection Block
 *
 * @ORM\Entity
 */
class RelatedCollectionBlock extends CollectionBlock
{
    /**
     * @return string
     */
    public function getBlockType()
    {
        return 'related_collection';
    }
}
