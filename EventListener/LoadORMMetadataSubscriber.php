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
    protected $classes;

    /**
     * Constructor
     *
     * @param array $classes
     */
    public function __construct($classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();
//        if ($metadata->name == 'AppBundle\Entity\User') {
            $this->process($metadata, $eventArgs->getEntityManager()->getConfiguration());
//        }
//
//        if (!$metadata->isMappedSuperclass) {
//            $this->setAssociationMappings($metadata, $eventArgs->getEntityManager()->getConfiguration());
//        } else {
//            $this->unsetAssociationMappings($metadata);
//        }
    }

    private function process(ClassMetadataInfo $metadata, $configuration)
    {
        foreach($this->classes as $entity) {
            if (isset($entity['model']) && $entity['model'] === $metadata->getName()) {
                 $metadata->isMappedSuperclass = false;
//                $this->mergeFieldMappings($metadata, $configuration);
                if (isset($entity['repository'])) {
                    $metadata->setCustomRepositoryClass($entity['repository']);
                }
            }
        }
    }

    private function mergeFieldMappings(ClassMetadataInfo $metadata, $configuration)
    {
        foreach (class_parents($metadata->getName()) as $parent) {
            $parentMetadata = new ClassMetadata(
                $parent,
                $configuration->getNamingStrategy()
            );
            if (in_array($parent, $configuration->getMetadataDriverImpl()->getAllClassNames())) {
                $configuration->getMetadataDriverImpl()->loadMetadataForClass($parent, $parentMetadata);
                if ($parentMetadata->isMappedSuperclass) {
                    foreach ($parentMetadata->fieldMappings as $key => $value) {
                        if (!isset($metadata->fieldMappings[$key])) {
                            $metadata->fieldMappings[$key] = $value;
                        }
                    }
                    foreach ($parentMetadata->getFieldNames() as $key => $value) {
                        if (!isset($metadata->fieldNames[$key])) {
                            $metadata->fieldNames[$key] = $value;
                        }
                    }
                    foreach ($parentMetadata->getColumnNames() as $key => $value) {
                        if (!isset($metadata->columnNames[$key])) {
                            $metadata->columnNames[$key] = $value;
                        }
                    }
                }
            }
        }
    }

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
    private function unsetAssociationMappings(ClassMetadataInfo $metadata)
    {
        foreach ($metadata->getAssociationMappings() as $key => $value) {
            if ($this->hasRelation($value['type'])) {
                unset($metadata->associationMappings[$key]);
            }
        }
    }
    private function hasRelation($type)
    {
        return in_array(
            $type,
            array(
                ClassMetadataInfo::MANY_TO_MANY,
                ClassMetadataInfo::ONE_TO_MANY,
                ClassMetadataInfo::ONE_TO_ONE,
            ),
            true
        );
    }
}
