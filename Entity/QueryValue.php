<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\SerializerBuilder;

/**
 * Query Value
 *
 * @ORM\Entity
 */
class QueryValue extends Value
{
    /**
     * Get Rule
     *
     * @return Opifer\RulesEngine\Rule\Rule
     */
    public function getRule()
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->deserialize($this->value, 'Opifer\RulesEngine\Rule\Rule', 'json');
    }
}
