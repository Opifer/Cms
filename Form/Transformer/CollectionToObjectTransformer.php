<?php

namespace Opifer\EavBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Form\DataTransformerInterface;

class CollectionToObjectTransformer implements DataTransformerInterface
{
    /**
     * Transforms an ArrayCollection Object to a single Object.
     *
     * @param \Doctrine\ORM\PersistentCollection $values
     *
     * @return Object
     */
    public function transform($values)
    {
        if (null === $values || !($values instanceof Collection)) {
            return null;
        }

        return $values->first();
    }

    /**
     * Transforms a single Object to an ArrayCollection
     *
     * @param Object $value
     *
     * @return ArrayCollection
     */
    public function reverseTransform($value)
    {
        if ($value instanceof Collection) {
            return $value;
        }

        $collection = new ArrayCollection();
        $collection->add($value);

        return $collection;
    }
}
