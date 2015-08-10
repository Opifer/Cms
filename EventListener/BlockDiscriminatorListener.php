<?php

namespace Opifer\EavBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Opifer\EavBundle\ValueProvider\Pool;

/**
 * Class BlockDiscriminatorListener
 *
 * @package Opifer\EavBundle\EventListener
 */
class BlockDiscriminatorListener
{
    /** @var BlockManager $pool */
    protected $blockManager;

    /**
     * Constructor
     *
     * @param Pool $pool
     */
    public function __construct(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    /**
     * loadClassMetadata event
     *
     * Retrieves the discriminatorMap from the BlockManagar, so we can
     * add entities to the discriminatorMap without adjusting the annotations
     * in the Block entity.
     *
     * @param LoadClassMetadataEventArgs $args
     *
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();
        if ($metadata->name == 'Opifer\\CmsBundle\\Entity\\Block') {
            $metadata->setDiscriminatorMap($this->getDiscriminatorMap());
        }
    }

    /**
     * Transforms the registered blocks into a discriminatorMap
     *
     * @return array
     */
    public function getDiscriminatorMap()
    {
        $map = array();
        foreach ($this->blockManager->getValues() as $alias => $value) {
            $map[$alias] = $value->getEntity();
        }

        return $map;
    }
}
