<?php
namespace Opifer\CmsBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Table prefix subscriber
 *
 * Adds a prefix to the entity's table name
 */
class TablePrefixSubscriber implements EventSubscriber
{
    protected $prefix = '';

    /**
     * Constructor
     *
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['loadClassMetadata'];
    }

    /**
     * Load class meta data event
     *
     * @param LoadClassMetadataEventArgs $args
     *
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();

        // Only add the prefixes to Opifer entities.
        if (FALSE !== strpos($classMetadata->namespace, 'Opifer')) {
            // Do not re-apply the prefix when it's already prefixed
            if (false === strpos($classMetadata->getTableName(), $this->prefix)) {
                $tableName = $this->prefix.$classMetadata->getTableName();
                $classMetadata->setPrimaryTable(['name' => $tableName]);
            }

            foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
                if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide'] == true) {
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];

                    // Do not re-apply the prefix when the association is already prefixed
                    if (false !== strpos($mappedTableName, $this->prefix)) {
                        continue;
                    }

                    $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix.$mappedTableName;
                }
            }
        }
    }
}
