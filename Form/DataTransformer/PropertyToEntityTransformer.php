<?php

namespace Opifer\CmsBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyToEntityTransformer implements DataTransformerInterface
{
    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $config;

    /**
     * @param EntityManager $em
     * @param array $config
     */
    function __construct(EntityManager $em, array $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($entity, $this->config['property']);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        $entity = $this->em->getRepository($this->config['class'])->findOneBy([
            $this->config['property'] => $value
        ]);

        return $entity;
    }
}
