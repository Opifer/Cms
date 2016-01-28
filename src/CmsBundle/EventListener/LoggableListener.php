<?php

namespace Opifer\CmsBundle\EventListener;

use Doctrine\Common\EventArgs;
use Gedmo\Loggable\LoggableListener as BaseLoggableListener;
use Opifer\ContentBundle\Block\BlockOwnerInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Loggable listener
 */
class LoggableListener extends BaseLoggableListener
{
    /**
     * Keeps version for tree in memory in case any other affiliated
     * block gets persisted.
     *
     * @var integer
     */
    protected $rootVersion;

    /**
     * Keeps changesets in memory in case an object gets flushed multiple
     * times in a single request.
     *
     * @var array
     */
    protected $changeSets;

    /**
     * Handle any custom LogEntry functionality that needs to be performed
     * before persisting it
     *
     * @param object $logEntry The LogEntry being persisted
     * @param object $object   The object being Logged
     */
    protected function prePersistLogEntry($logEntry, $object)
    {
        if ($object instanceof BlockInterface && $object->getRootVersion()) {
            $this->rootVersion = $object->getRootVersion();
        }
    }

    /**
     * Create a new Log instance
     *
     * @param string          $action
     * @param object          $object
     * @param LoggableAdapter $ea
     *
     * @return \Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry|null
     */
    protected function createLogEntry($action, $object, LoggableAdapter $ea)
    {
        $om = $ea->getObjectManager();
        $wrapped = AbstractWrapper::wrap($object, $om);
        $meta = $wrapped->getMetadata();

        // Filter embedded documents
        if (isset($meta->isEmbeddedDocument) && $meta->isEmbeddedDocument) {
            return;
        }

        // TODO replace with more generic interface
        if ($object instanceof BlockInterface && $object->isPublish()) {
            return;
        }

        if ($config = $this->getConfiguration($om, $meta->name)) {
            $logEntryClass = $this->getLogEntryClass($ea, $meta->name);
            $logEntryMeta = $om->getClassMetadata($logEntryClass);
            /** @var \Gedmo\Loggable\Entity\LogEntry $logEntry */
            $logEntry = $logEntryMeta->newInstance();

            $logEntry->setAction($action);
            $logEntry->setUsername($this->username);
            $logEntry->setObjectClass($meta->name);
            $logEntry->setLoggedAt();

            // check for the availability of the primary key
            /** @var UnitOfWork $uow */
            $uow = $om->getUnitOfWork();
            if ($action === self::ACTION_CREATE && $ea->isPostInsertGenerator($meta)) {
                $this->pendingLogEntryInserts[spl_object_hash($object)] = $logEntry;
            } else {
                $logEntry->setObjectId($wrapped->getIdentifier());
            }
            $newValues = array();
            if (isset($config['versioned'])) { //$action !== self::ACTION_REMOVE &&
                $newValues = $this->getObjectChangeSetData($ea, $object, $logEntry);
                $logEntry->setData($newValues);
                if (isset($this->changeSets[spl_object_hash($object)])) {
                    // Recalculate changeset data from scratch because we are overwriting
                    // and we are at risk to store only the latest change if we are flushing
                    // the object multiple times in the same request. This happens for example
                    // when we are sorting objects.
                    $prevValues = $this->changeSets[spl_object_hash($object)];
                    $newValues = array_merge($prevValues, $newValues);
                } else {
                    $this->changeSets[spl_object_hash($object)] = $newValues;
                }
            }

            if($action === self::ACTION_UPDATE && 0 === count($newValues) && !($object instanceof BlockInterface)) {
                return null;
            }

            $version = 1;
            if ($action !== self::ACTION_CREATE) {
                $version = $ea->getNewVersion($logEntryMeta, $object);
                if (empty($version)) {
                    // was versioned later
                    $version = 1;
                }
            }
            $logEntry->setVersion($version);

            $this->prePersistLogEntry($logEntry, $object);


            // TODO replace with more generic code
            if ($object instanceof BlockInterface) {
                // Check if this rootVersion has an Entry already, if so, update that instead
                $existingLogEntry = $om->getRepository($logEntryClass)->findOneBy(['rootVersion' => $this->rootVersion, 'objectId' => $object->getId()]);

                if ($existingLogEntry) {
                    unset($this->pendingLogEntryInserts[spl_object_hash($object)]);
                    $logEntry = $existingLogEntry;
                    $logEntry->setUsername($this->username);
                    $logEntry->setObjectClass($meta->name);
                    $logEntry->setLoggedAt();
                    $logEntry->setData($newValues);
                } else if($action === self::ACTION_UPDATE && 0 === count($newValues)) {
                    return null;
                }

                if ($action !== self::ACTION_CREATE) {
                    $this->resetVersionedData($ea, $object);
//                    $uow->detach($object);
                }

                // Reset version number to that of owner Document, so we keep a tree
                // of blocks together on a single version #
                $logEntry->setRootVersion($this->rootVersion);
                $logEntry->setRootId(($object instanceof BlockOwnerInterface || $object->isShared()) ? $object->getId() : $object->getOwner()->getId());
//                $uow->computeChangeSet($logEntryMeta, $logEntry);
            }

            $om->persist($logEntry);
            $uow->computeChangeSet($logEntryMeta, $logEntry);

            return $logEntry;
        }

        return null;
    }


    protected function resetVersionedData($ea, $object)
    {
        $om        = $ea->getObjectManager();
        $wrapped   = AbstractWrapper::wrap($object, $om);
        $objectMeta = $wrapped->getMetadata();
        $meta      = $wrapped->getMetadata();
        $config    = $this->getConfiguration($om, $meta->name);
        /** @var UnitOfWork $uow */
        $uow       = $om->getUnitOfWork();

        foreach ($ea->getObjectChangeSet($uow, $object) as $field => $changes) {
            if (empty($config['versioned']) || !in_array($field, $config['versioned'])) {
                continue;
            }

            $value = $changes[0];
//            $this->mapValue($om, $objectMeta, $field, $value);
            $wrapped->setPropertyValue($field, $value);
        }

//        $uow->computeChangeSet($objectMeta, $object);
    }


    /**
     * @param ClassMetadata $objectMeta
     * @param string        $field
     * @param mixed         $value
     */
    protected function mapValue($om, ClassMetadata $objectMeta, $field, &$value)
    {
        if (!$objectMeta->isSingleValuedAssociation($field)) {
            return;
        }

        $mapping = $objectMeta->getAssociationMapping($field);
        $value   = $value ? $om->getReference($mapping['targetEntity'], $value) : null;
    }
}