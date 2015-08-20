<?php

namespace Opifer\ContentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * BlockLogEntry class
 *
 * @ORM\Table(name="block_log_entry")
 * @ORM\Entity(repositoryClass="Opifer\ContentBundle\Repository\BlockLogEntryRepository")
 */
class BlockLogEntry extends AbstractLogEntry
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="root_version")
     */
    protected $rootVersion;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="root_id", nullable=true)
     */
    protected $rootId;

    /**
     * @return int
     */
    public function getRootVersion()
    {
        return $this->rootVersion;
    }

    /**
     * @param int $rootVersion
     */
    public function setRootVersion($rootVersion)
    {
        $this->rootVersion = $rootVersion;
    }

    /**
     * @return int
     */
    public function getRootId()
    {
        return $this->rootId;
    }

    /**
     * @param int $rootId
     */
    public function setRootId($rootId)
    {
        $this->rootId = $rootId;
    }
}