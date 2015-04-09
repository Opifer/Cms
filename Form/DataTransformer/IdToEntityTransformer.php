<?php

namespace Opifer\ContentBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class IdToEntityTransformer implements DataTransformerInterface
{
    protected $entityManager;

    /**
     * Constructor
     *
     * @param object $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
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
        if(is_null($entity)) {
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
        if(null == $id) {
            return null;
        }
        
        
        $entity = $this->entityManager->getRepository()->findById($id);
        
        if($entity) {
            return $entity[0];
        }
    }
}
