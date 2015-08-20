<?php

namespace Opifer\ContentBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class DraftVersionFilter
 *
 * The DraftVersionFilter adds the condition necessary to
 * filter entities which are created as draft (version 0)
 *
 * @package Gedmo\SoftDeleteable\Filter
 */
class DraftVersionFilter extends SQLFilter
{
    /**
     * @param ClassMetaData $targetEntity
     * @param string        $targetTableAlias
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        // Check if the entity implements the LocalAware interface
        if (!$targetEntity->reflClass->implementsInterface('Opifer\ContentBundle\Block\DraftVersionInterface')) {
            return "";
        }

        return "{$targetTableAlias}.version > 0";
    }

}
