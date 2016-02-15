<?php

namespace Opifer\ContentBundle\Repository;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Gedmo\Tool\Wrapper\EntityWrapper;

/**
 * Class BlockLogEntryRepository
 *
 * @package Opifer\ContentBundle\Repository
 */
class BlockLogEntryRepository extends LogEntryRepository
{

    /**
     * @param integer $ownerId
     * @param integer $rootVersion
     */
    public function discardAll($rootId, $rootVersion)
    {
        $dql = "DELETE FROM Opifer\ContentBundle\Entity\BlockLogEntry log";
        $dql .= " WHERE log.rootId = :rootId";
        $dql .= " AND log.rootVersion = :rootVersion";

        $q = $this->_em->createQuery($dql);
        $q->setParameters(compact('rootId', 'rootVersion'));
        $q->execute();
    }

    /**
     * Returns a list of BlockLogEntries distinct by rootId
     *
     * @param integer $rootId
     *
     * @return ArrayCollection
     */
    public function findDistinctByRootId($rootId)
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.rootId = :rootId')
            ->groupBy('l.rootVersion')
            ->setParameter('rootId', $rootId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Loads all log entries for the given entity
     *
     * @param object  $entity
     * @param integer $rootVersion
     *
     * @return array
     */
    public function getLogEntriesRoot($entity, $rootVersion = null)
    {
        $q = $this->getLogEntriesQueryRoot($entity, $rootVersion);

        return $q->getResult();
    }

    /**
     * Get the query for loading of log entries
     *
     * @param object  $entity
     * @param integer $rootVersion
     *
     * @return Query
     */
    public function getLogEntriesQueryRoot($entity, $rootVersion = null)
    {
        $wrapped = new EntityWrapper($entity, $this->_em);
        $objectClass = $wrapped->getMetadata()->name;
        $meta = $this->getClassMetadata();
        $dql = "SELECT log FROM {$meta->name} log";
        $dql .= " WHERE log.objectId = :objectId";
        $dql .= " AND log.objectClass = :objectClass";
        $dql .= " AND log.rootVersion <= :rootVersion";
        $dql .= " ORDER BY log.version DESC";

        $objectId = $wrapped->getIdentifier();
        $q = $this->_em->createQuery($dql);
        $q->setParameters(compact('objectId', 'objectClass', 'rootVersion'));

        return $q;
    }


    public function nullifyLogEntry($entity, $rootVersion)
    {
        $wrapped = new EntityWrapper($entity, $this->_em);
        $objectClass = $wrapped->getMetadata()->name;
        $objectId = $wrapped->getIdentifier();

        $logEntry = $this->findOneBy(compact('objectId', 'objectClass', 'rootVersion'));

        if ($logEntry) {
            $logEntry->setData(null);
            $this->getEntityManager()->flush($logEntry);
        }
    }
}