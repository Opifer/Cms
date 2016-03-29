<?php

namespace Opifer\CmsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class LoadORMMetadataSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    private $entities;

    /**
     * @param array $entities
     */
    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        // Avoid unrelated entities
        if (!in_array(strtolower($metadata->getName()), $this->getOverriddenClassesArray()) &&
            !in_array($metadata->getName(), $this->getSimpleModelArray())) {
            return;
        }

        $this->convertToEntityIfNeeded($metadata);

        if (!$metadata->isMappedSuperclass) {
            $this->setAssociationMappings($metadata, $eventArgs->getEntityManager()->getConfiguration());
        } else {
            $this->unsetAssociationMappings($metadata);
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function convertToEntityIfNeeded(ClassMetadataInfo $metadata)
    {
        foreach ($this->entities as $alias => $resourceMetadata) {
            if ($metadata->getName() !== $resourceMetadata['model']) {
                continue;
            }

            //if ($resourceMetadata->hasClass('repository')) {
            //    $metadata->setCustomRepositoryClass($resourceMetadata->getClass('repository'));
            //}

            $metadata->isMappedSuperclass = false;
        }
    }

    public function getSimpleModelArray()
    {
        $models = [];

        foreach ($this->entities as $resourceMetadata) {
            $models[] = $resourceMetadata['model'];
        }

        return $models;
    }

    public function getOverriddenClassesArray()
    {
        $models = [];

        foreach ($this->entities as $shortClassName => $resourceMetadata) {
            $models[] = strtolower('Opifer\\CmsBundle\\Entity\\'.$shortClassName);
        }

        return $models;
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param $configuration
     */
    private function setAssociationMappings(ClassMetadataInfo $metadata, $configuration)
    {
        foreach (class_parents($metadata->getName()) as $parent) {
            $parentMetadata = new ClassMetadata(
                $parent,
                $configuration->getNamingStrategy()
            );

            if (in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);
                if ($parentMetadata->isMappedSuperclass) {
                    foreach ($parentMetadata->getAssociationMappings() as $key => $value) {
                        if ($this->hasRelation($value['type'])) {
                            $metadata->associationMappings[$key] = $value;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param ClassMetadataInfo $metadata
     */
    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach ($metadata->getAssociationMappings() as $key => $value) {
            if ($this->hasRelation($value['type'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }
    /**
     * @param $type
     *
     * @return bool
     */
    private function hasRelation($type)
    {
        return in_array(
            $type,
            [
                ClassMetadataInfo::MANY_TO_MANY,
                ClassMetadataInfo::ONE_TO_MANY,
                ClassMetadataInfo::MANY_TO_ONE,
                ClassMetadataInfo::ONE_TO_ONE,
            ],
            true
        );
    }
}
