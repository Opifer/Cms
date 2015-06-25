<?php

namespace Opifer\ContentBundle\Form\DataTransformer;

use Opifer\ContentBundle\Model\ContentManager;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class IdToContentTransformer implements DataTransformerInterface
{
    /**
     * @var ContentManager
     */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(ContentManagerInterface $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * Transforms an entity to id
     *
     * @param object $entity
     *
     * @return array
     */
    public function transform($entity)
    {
        if (is_null($entity)) {
            return null;
        }

        return $entity->getId();
    }

    /**
     * Transforms an id to entity
     *
     * @param integer $id
     *
     * @return object
     */
    public function reverseTransform($id)
    {
        if (null == $id) {
            return null;
        }

        $entity = $this->contentManager->getRepository()->find($id);

        return $entity;
    }
}
