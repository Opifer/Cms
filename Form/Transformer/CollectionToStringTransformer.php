<?php

namespace Opifer\EavBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CollectionToStringTransformer implements DataTransformerInterface
{
    protected $object;

    /**
     * Constructor
     *
     * @param Object $object
     */
    public function __construct($object = null)
    {
        if (!is_null($object)) {
            $this->object = $object;
        }
    }

    /**
     * Transforms an ArrayCollection Object to a single Object.
     *
     * @param \Doctrine\ORM\PersistentCollection $value
     *
     * @return Object
     */
    public function transform($value)
    {
        if (null === $value || !($value instanceof Collection)) {
            return '';
        }

        $string = '';
        foreach ($value as $item) {
            $string .= $item->getId();

            if ($value->last() != $item) {
                $string .= ',';
            }
        }

        return $string;
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
        // if (null === $this->object) {
        //     throw new TransformationFailedException('Theres no object set for the reverse transform');
        // }

        // $collection = new ArrayCollection();

        // $values = explode(',', $value);
        // foreach ($values as $id) {
        //     // @todo Retrieve the entity by it's id and add it to the collection
        // }

        // return $collection;
        return $value;
    }
}
