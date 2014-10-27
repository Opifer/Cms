<?php

namespace Opifer\EavBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

use Opifer\EavBundle\ValueProvider\Pool;

class DiscriminatorListener
{
    /** @var Pool $pool */
    protected $pool;

    /**
     * Constructor
     *
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * loadClassMetadata event
     *
     * Retrieves the discriminatorMap from the value provider pool, so we can
     * add entities to the discriminatorMap without adjusting the annotations
     * in the Value entity.
     *
     * @param LoadClassMetadataEventArgs $args
     *
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();
        if ($metadata->name == 'Opifer\\EavBundle\\Entity\\Value') {
            $metadata->setDiscriminatorMap($this->getDiscriminatorMap());
        }
    }

    /**
     * Transforms the provider values into a discriminatorMap
     *
     * @return array
     */
    public function getDiscriminatorMap()
    {
        $map = array();
        foreach ($this->pool->getValues() as $alias => $value) {
            $map[$alias.'value'] = $value->getEntity();
        }

        return $map;
    }
}
