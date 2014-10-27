<?php

namespace Opifer\EavBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\SerializerBuilder;

use Opifer\EavBundle\Eav\ValueInterface;

/**
 * Query Value
 *
 * @ORM\Entity
 */
class QueryValue extends Value implements ValueInterface
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
