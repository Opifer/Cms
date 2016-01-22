<?php

namespace Opifer\ContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\BasicEntityPersister;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\Persisters\Entity\JoinedSubclassPersister;
use Doctrine\ORM\Utility\PersisterHelper;
use SimpleThings\EntityAudit\EventListener\LogRevisionsListener as BaseLogRevisionsListener;

class LogRevisionsListener extends BaseLogRevisionsListener
{
    /**
     * Keeps version for tree in memory in case any other affiliated
     * block gets persisted.
     *
     * @var integer
     */
    protected $rootVersion;

    /**
     * @var array
     */
    protected $updateRevisionSQL = array();

    /**
     * @param ClassMetadata $class
     * @param array         $entityData
     * @param string        $revType
     */
    protected function saveRevisionEntityData($class, $entityData, $revType)
    {
        $revision = (isset($entityData['block'])) ? $entityData['block']->getVersion() : 0;

        $params = array($revision, $revType);
        $types = array(\PDO::PARAM_INT, \PDO::PARAM_STR);

        $fields = array();

        foreach ($class->associationMappings AS $field => $assoc) {
            if ($class->isInheritanceTypeJoined() && $class->isInheritedAssociation($field)) {
                continue;
            }
            if (! (($assoc['type'] & ClassMetadata::TO_ONE) > 0 && $assoc['isOwningSide'])) {
                continue;
            }

            $data = isset($entityData[$field]) ? $entityData[$field] : null;
            $relatedId = false;

            if ($data !== null && $this->uow->isInIdentityMap($data)) {
                $relatedId = $this->uow->getEntityIdentifier($data);
            }

            $targetClass = $this->em->getClassMetadata($assoc['targetEntity']);

            foreach ($assoc['sourceToTargetKeyColumns'] as $sourceColumn => $targetColumn) {
                $fields[$sourceColumn] = true;
                if ($data === null) {
                    $params[] = null;
                    $types[] = \PDO::PARAM_STR;
                } else {
                    $params[] = $relatedId ? $relatedId[$targetClass->fieldNames[$targetColumn]] : null;
                    $types[] = $targetClass->getTypeOfColumn($targetColumn);
                }
            }
        }

        foreach ($class->fieldNames AS $field) {
            if (array_key_exists($field, $fields)) {
                continue;
            }

            if ($class->isInheritanceTypeJoined()
                && $class->isInheritedField($field)
                && ! $class->isIdentifier($field)
            ) {
                continue;
            }

            $params[] = isset($entityData[$field]) ? $entityData[$field] : null;
            $types[] = $class->fieldMappings[$field]['type'];
        }

        if ($class->isInheritanceTypeSingleTable()) {
            $params[] = $class->discriminatorValue;
            $types[] = $class->discriminatorColumn['type'];
        } elseif ($class->isInheritanceTypeJoined()
            && $class->name == $class->rootEntityName
        ) {
            $params[] = $entityData[$class->discriminatorColumn['name']];
            $types[] = $class->discriminatorColumn['type'];
        }

        if ($class->isInheritanceTypeJoined() && $class->name != $class->rootEntityName) {
            $entityData[$class->discriminatorColumn['name']] = $class->discriminatorValue;
            $this->saveRevisionEntityData(
                $this->em->getClassMetadata($class->rootEntityName),
                $entityData,
                $revType
            );
        }

        $tableName = $tableName = $this->config->getTableName($class);
        $query = "SELECT id FROM " . $tableName . " WHERE id = ? AND rev = ?";

        // If there is already a log for this version, update
        $existingLogEntry = $this->em->getConnection()->fetchAssoc($query, [$entityData['id'], $revision]);
        if ($existingLogEntry) {
            $params[] = $entityData['id'];
            $this->conn->executeUpdate($this->getUpdateRevisionSQL($class), $params, $types);
        } else {
            $this->conn->executeUpdate($this->getInsertRevisionSQL($class), $params, $types);
        }
    }

    /**
     * @param ClassMetadata $class
     *
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getUpdateRevisionSQL($class)
    {
        if (! isset($this->updateRevisionSQL[$class->name])) {
            $tableName = $this->config->getTableName($class);

            $sql = "UPDATE " . $tableName . " SET " .
                $this->config->getRevisionFieldName() . " = ?, " . $this->config->getRevisionTypeFieldName() . " = ?";

            $fields = array();

            foreach ($class->associationMappings as $field => $assoc) {
                if ($class->isInheritanceTypeJoined() && $class->isInheritedAssociation($field)) {
                    continue;
                }

                if (($assoc['type'] & ClassMetadata::TO_ONE) > 0 && $assoc['isOwningSide']) {
                    foreach ($assoc['targetToSourceKeyColumns'] as $sourceCol) {
                        $fields[$sourceCol] = true;
                        $sql .= ', ' . $sourceCol. ' = ?';
                    }
                }
            }

            foreach ($class->fieldNames as $field) {
                if (array_key_exists($field, $fields)) {
                    continue;
                }

                if ($class->isInheritanceTypeJoined()
                    && $class->isInheritedField($field)
                    && ! $class->isIdentifier($field)
                ) {
                    continue;
                }

                $type = Type::getType($class->fieldMappings[$field]['type']);
                $placeholder = (! empty($class->fieldMappings[$field]['requireSQLConversion']))
                    ? $type->convertToDatabaseValueSQL('?', $this->platform)
                    : '?';
                $sql .= ', ' . $class->getQuotedColumnName($field, $this->platform) . ' = '.$placeholder;
            }

            if (($class->isInheritanceTypeJoined() && $class->rootEntityName == $class->name)
                || $class->isInheritanceTypeSingleTable()
            ) {
                $sql .= ', ' . $class->discriminatorColumn['name']. ' = ?';
            }

            $sql .= " WHERE id = ?";

            $this->updateRevisionSQL[$class->name] = $sql;
        }

        return $this->updateRevisionSQL[$class->name];
    }
}
